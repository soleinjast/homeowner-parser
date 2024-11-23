<?php

namespace Modules\HomeownerProcessor\Tests\Unit;

use Modules\HomeownerProcessor\Commands\ProcessHomeownersCommand;
use Modules\HomeownerProcessor\Core\HomeownerProcessor;
use Modules\HomeownerProcessor\Exceptions\FileNotFoundException;
use Modules\HomeownerProcessor\Services\CSVReader;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class CSVReaderTest extends TestCase
{
    protected CSVReader $csvReader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->csvReader = new CSVReader();
    }

    /**
     * @throws FileNotFoundException
     */
    public function testReadValidFile(): void
    {
        $filePath = __DIR__ . '/valid.csv';
        file_put_contents($filePath, "Name\nJohn Doe");
        $rows = $this->csvReader->readFile($filePath);
        $this->assertCount(1, $rows);
        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testCommandHandlesMissingFile(): void
    {
        $processor = $this->createMock(HomeownerProcessor::class);
        $processor->method('process')->willThrowException(new \RuntimeException('File not found'));
        $command = new ProcessHomeownersCommand($processor);
        $this->artisan('homeowners:process invalid.csv')
            ->expectsOutput('Processing failed: The specified file does not exist.')
            ->assertExitCode(1);
    }

    /**
     * @throws FileNotFoundException
     */
    public function testReadEmptyFile(): void
    {
        $filePath = __DIR__ . '/valid.csv';
        file_put_contents($filePath, "");

        $rows = $this->csvReader->readFile($filePath);

        $this->assertEmpty($rows);
        unlink($filePath);
    }

    public function testReadMissingFileThrowsException(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->csvReader->readFile('/nonexistent/file.csv');
    }
}
