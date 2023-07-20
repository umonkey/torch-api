<?php

declare(strict_types=1);

namespace App\Tools\Tests;

use App\Core\AbstractTestCase;
use App\Database\Exceptions\DatabaseException;
use App\Tools\ExportData;
use JsonException;
use ZipArchive;

class ExportDataTests extends AbstractTestCase
{
    private readonly ExportData $exporter;

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function testExport(): void
    {
        $this->fixture('001.yaml');

        $fileName = tempnam('/tmp', 'export-test-');
        self::assertIsString($fileName);

        try {
            $this->exporter->export($fileName);

            $zip = new ZipArchive();
            $zip->open($fileName, ZipArchive::RDONLY);

            self::assertEquals(2, count($zip));

            self::assertEquals([
                'type' => 'page',
                'data' => [
                    'created' => 12345,
                    'updated' => 12346,
                    'id' => 'test',
                    'text' => 'Hello, world.',
                ],
            ], $this->extractFile($zip, 0));

            self::assertEquals([
                'type' => 'user',
                'data' => [
                    'created_at' => 12345,
                    'login_at' => 12346,
                    'id' => 'test',
                    'email' => 'test@example.com',
                    'password' => 'secret',
                ],
            ], $this->extractFile($zip, 1));
        } finally {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->exporter = $this->container->get(ExportData::class);
    }

    /**
     * @return mixed[]
     * @throws JsonException
     */
    private function extractFile(ZipArchive $zip, int $index): array
    {
        $contents = $zip->getFromIndex($index);
        self::assertIsString($contents);
        return json_decode($contents, true, flags: \JSON_THROW_ON_ERROR);
    }
}
