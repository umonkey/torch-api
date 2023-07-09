<?php

declare(strict_types=1);

namespace App\Core\Testing;

use App\Database\Entities\UserEntity;
use App\Database\Repositories\UserRepository;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class FixtureFactory
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function locate(string $fileName): ?string
    {
        $stack = debug_backtrace(limit: 10);

        foreach ($stack as $frame) {
            if (isset($frame['file'])) {
                $folderName = dirname($frame['file']);
                $filePath = $folderName . '/fixtures/' . $fileName;

                if (file_exists($filePath)) {
                    return $filePath;
                }
            }
        }

        return null;
    }

    /**
     * @throws ParseException
     * @throws RuntimeException
     */
    public function processFile(string $filePath): void
    {
        $yaml = file_get_contents($filePath);

        if (!is_string($yaml)) {
            throw new RuntimeException('error parsing fixture file');
        }

        $fixture = Yaml::parse($yaml);

        $this->setUpTables($fixture['tables'] ?? []);
    }

    /**
     * @param array<string,mixed> $tables
     * @throws RuntimeException
     */
    private function setUpTables(array $tables): void
    {
        foreach ($tables as $tableName => $items) {
            foreach ($items as $item) {
                try {
                    match ($tableName) {
                        'users' => $this->users->add(
                            new UserEntity($item),
                        ),

                        default => throw new RuntimeException(sprintf("don't know how to add entities to table %s", $tableName)),
                    };
                } catch (Throwable $e) {
                    throw new RuntimeException(sprintf('Error setting up table %s: %s', $tableName, $e->getMessage()));
                }
            }
        }
    }
}
