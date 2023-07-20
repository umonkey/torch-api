<?php

declare(strict_types=1);

namespace App\Tools;

use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\PageRepository;
use App\Database\Repositories\UserRepository;
use JsonException;
use ZipArchive;

class ExportData extends AbstractCommand
{
    public function __construct(private readonly PageRepository $pages, private readonly UserRepository $users)
    {
    }

    /**
     * @param string[] $args
     * @throws DatabaseException
     * @throws JsonException
     */
    public function __invoke(array $args): void
    {
        $fileName = sprintf('archive-%s.zip', strftime('%Y%m%d-%H%M'));

        $zip = new ZipArchive();
        $zip->open($fileName, ZipArchive::CREATE);

        self::exportPages($zip);
        self::exportUsers($zip);

        $zip->close();

        fprintf(STDOUT, "Wrote %s\n", $fileName);
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    private function exportPages(ZipArchive $zip): void
    {
        foreach ($this->pages->iter() as $item) {
            $data = [
                'type' => 'page',
                'data' => $item->toArray(),
            ];

            $json = json_encode($data, \JSON_THROW_ON_ERROR);

            $key = sha1($item->getId());
            $fileName = sprintf('pages/%s.json', $key);

            $zip->addFromString($fileName, $json);

            fprintf(STDOUT, "Wrote page %s as %s\n", $item->getId(), $fileName);
        }
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    private function exportUsers(ZipArchive $zip): void
    {
        foreach ($this->users->iter() as $item) {
            $data = [
                'type' => 'user',
                'data' => $item->toArray(),
            ];

            $json = json_encode($data, \JSON_THROW_ON_ERROR);

            $key = sha1($item->getId());
            $fileName = sprintf("users/%s.json", $key);

            $zip->addFromString($fileName, $json);

            fprintf(STDOUT, "Wrote user %s as %s\n", $item->getId(), $fileName);
        }
    }
}
