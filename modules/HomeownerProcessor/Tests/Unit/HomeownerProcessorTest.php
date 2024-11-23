<?php

namespace Modules\HomeownerProcessor\Tests\Unit;
use Modules\HomeownerProcessor\Core\HomeownerProcessor;
use Modules\HomeownerProcessor\Exceptions\FileNotFoundException;
use Modules\HomeownerProcessor\Exceptions\InvalidRowFormatException;
use Modules\HomeownerProcessor\Services\CSVReader;
use Modules\HomeownerProcessor\Services\HomeownerParser;
use Modules\HomeownerProcessor\Services\PersonExporter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class HomeownerProcessorTest extends TestCase
{
    protected CSVReader $csvReader;
    protected HomeownerParser $parser;
    protected PersonExporter $exporter;
    protected HomeownerProcessor $processor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->csvReader = $this->createMock(CSVReader::class);
        $this->parser = $this->createMock(HomeownerParser::class);
        $this->exporter = $this->createMock(PersonExporter::class);

        $this->processor = new HomeownerProcessor($this->csvReader, $this->parser, $this->exporter);
    }

    public function testProcessValidFile(): void
    {
        $this->csvReader->method('readFile')->willReturn([['Mr. John Doe']]);
        $this->parser->method('parseRow')->willReturn([
            ['title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Doe'],
        ]);
        $this->exporter->expects($this->once())->method('exportToJson');

        $this->processor->process('/valid/file.csv');
    }


    public function testProcessHandlesFileNotFound(): void
    {
        $this->csvReader->method('readFile')->willThrowException(new FileNotFoundException());
        $this->expectException(\RuntimeException::class);

        $this->processor->process('/missing/file.csv');
    }

    public function testProcessHandlesInvalidRowFormat(): void
    {
        $this->csvReader->method('readFile')->willReturn([['']]);
        $this->parser->method('parseRow')->willThrowException(new InvalidRowFormatException());

        $this->expectException(\RuntimeException::class);
        $this->processor->process('/file.csv');
    }
}
