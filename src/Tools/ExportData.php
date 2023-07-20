<?php

declare(strict_types=1);

namespace App\Tools;

use App\Database\Exceptions\DatabaseException;
use App\Database\Repositories\PageRepository;
use App\Database\Repositories\UserRepository;
use RuntimeException;

class ExportData extends AbstractCommand
{
    public function __construct(private readonly PageRepository $pages, private readonly UserRepository $users)
    {
    }

    /**
     * @param string[] $args
     * @throws DatabaseException
     * @throws RuntimeException
     */
    public function __invoke(array $args): void
    {
        $folder = $args[0]
            ?? throw new RuntimeException('Target folder not specified.');

        self::exportPages($folder);
        self::exportUsers($folder);
    }

    /**
     * @throws DatabaseException
     */
    private function exportPages(string $folder): void
    {
        foreach ($this->pages->iter() as $item) {
            $data = [
                'type' => 'page',
                'data' => $item->toArray(),
            ];

            $json = json_encode($data);
            $key = sha1($item->getId());

            $fileName = sprintf("%s/page_%s.json", $folder, $key);
            file_put_contents($fileName, $json);

            fprintf(STDOUT, "Wrote page %s as %s\n", $item->getId(), $fileName);
        }
    }

    /**
     * @throws DatabaseException
     */
    private function exportUsers(string $folder): void
    {
        foreach ($this->users->iter() as $item) {
            $data = [
                'type' => 'user',
                'data' => $item->toArray(),
            ];

            $json = json_encode($data);
            $key = sha1($item->getId());

            $fileName = sprintf("%s/user_%s.json", $folder, $key);
            file_put_contents($fileName, $json);

            fprintf(STDOUT, "Wrote user %s as %s\n", $item->getId(), $fileName);
        }
    }
}
