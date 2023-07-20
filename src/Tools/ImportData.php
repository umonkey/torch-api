<?php

declare(strict_types=1);

namespace App\Tools;

use App\Database\Entities\PageEntity;
use App\Database\Entities\UserEntity;
use App\Database\Exceptions\DatabaseException;
use App\Database\Exceptions\DuplicateRecordException;
use App\Database\Repositories\PageRepository;
use App\Database\Repositories\UserRepository;
use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use ZipArchive;

class ImportData extends AbstractCommand
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PageRepository $pages,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * @param string[] $args
     * @throws DatabaseException
     * @throws JsonException
     * @throws RuntimeException
     * @codeCoverageIgnore
     */
    public function __invoke(array $args): void
    {
        $fileName = $args[0]
            ?? throw new RuntimeException('Zip archive file not specified.');

        $this->import($fileName);
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function import(string $fileName): void
    {
        $zip = new ZipArchive();
        $zip->open($fileName, ZipArchive::RDONLY);

        for ($idx = 0; $idx < count($zip); $idx++) {
            $this->extractFile($zip, $idx);
        }

        $zip->close();

        $this->logger->info(sprintf('Restored data from %s', $fileName));
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    private function extractFile(ZipArchive $zip, int $index): void
    {
        $file = $zip->getFromIndex($index);

        if (!is_string($file)) {
            // No idea how to trigger this from a test.
            // @codeCoverageIgnoreStart
            $this->logger->warning(sprintf('Could not process file %d.', $index));
            return;
            // @codeCoverageIgnoreEnd
        }

        $payload = json_decode($file, true, flags: \JSON_THROW_ON_ERROR);

        match ($payload['type'] ?? null) {
            'page' => $this->restorePage($payload['data']),
            'user' => $this->restoreUser($payload['data']),
            default => null,
        };
    }

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    private function restorePage(array $props): void
    {
        $page = new PageEntity($props);

        try {
            $this->pages->add($page);
        } catch (DuplicateRecordException) {
            $this->pages->update($page);
        }

        $this->logger->info(sprintf('Page %s restored.', $page->getId()));
    }

    /**
     * @param mixed[] $props
     * @throws DatabaseException
     */
    private function restoreUser(array $props): void
    {
        $user = new UserEntity($props);

        try {
            $this->users->add($user);
        } catch (DuplicateRecordException) {
            $this->users->update($user);
        }

        $this->logger->info(sprintf('User %s restored.', $user->getId()));
    }
}
