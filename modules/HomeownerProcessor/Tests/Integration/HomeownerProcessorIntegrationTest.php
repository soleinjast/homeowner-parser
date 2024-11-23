<?php

namespace Modules\HomeownerProcessor\Tests\Integration;
use Modules\HomeownerProcessor\Core\HomeownerProcessor;
use Modules\HomeownerProcessor\Services\CSVReader;
use Modules\HomeownerProcessor\Services\HomeownerParser;
use Modules\HomeownerProcessor\Services\PersonExporter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class HomeownerProcessorIntegrationTest extends TestCase
{
    public function testHomeownerProcessorEndToEnd(): void
    {
        // Create a fixture CSV file
        $filePath = __DIR__ . '/homeowners.csv';
        file_put_contents($filePath, "Name\nDr. John Doe & Mr and Mrs Smith\nMiss Emily Brown and Prof. Michael Green");

        // Set up components
        $csvReader = new CSVReader();
        $homeownerParser = new HomeownerParser();
        $output = new BufferedOutput();
        $exporter = new PersonExporter(new ArrayInput([]), $output);


        $processor = new HomeownerProcessor($csvReader, $homeownerParser, $exporter);
        $processor->process($filePath);


        $actualOutput = $output->fetch();
        $this->assertStringContainsString('Exported Homeowner Data by Row', $actualOutput);
        $this->assertStringContainsString('Row(Property) 1', $actualOutput);
        $this->assertStringContainsString('Dr', $actualOutput);
        $this->assertStringContainsString('John', $actualOutput);
        $this->assertStringContainsString('Doe', $actualOutput);
        $this->assertStringContainsString('Mrs', $actualOutput);
        $this->assertStringContainsString('Smith', $actualOutput);
        $this->assertStringContainsString('All data exported successfully!', $actualOutput);

        unlink($filePath);
    }

    public function testMalformedCsvRows(): void
    {
        $filePath = __DIR__ . '/malformed_rows.csv';
        file_put_contents($filePath, "Name\nDr. John Doe & Mr\n, ,\nProf. Emily Green");

        $csvReader = new CSVReader();
        $homeownerParser = new HomeownerParser();
        $output = new BufferedOutput();
        $exporter = new PersonExporter(new ArrayInput([]), $output);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The row format is invalid.');
        $processor = new HomeownerProcessor($csvReader, $homeownerParser, $exporter);
        $processor->process($filePath);

        unlink($filePath);
    }
}
