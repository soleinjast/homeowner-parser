<?php

namespace Modules\HomeownerProcessor\Tests\Unit;
use Modules\HomeownerProcessor\Services\PersonExporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class PersonExporterTest extends TestCase
{
    public function testExportToJsonProducesStyledOutput(): void
    {
        $data = [
            'Row(Property) 1' => [
                ['title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Doe'],
                ['title' => 'Mrs', 'first_name' => 'Jane', 'last_name' => 'Doe'],
            ],
            'Row(Property) 2' => [
                ['title' => 'Ms', 'first_name' => 'Emily', 'last_name' => 'Clark'],
            ],
        ];
        $bufferedOutput = new BufferedOutput();
        $exporter = new PersonExporter(new ArrayInput([]), $bufferedOutput);
        $exporter->exportToJson($data);
        $actualOutput = $bufferedOutput->fetch();
        $this->assertStringContainsString('Exported Homeowner Data by Row', $actualOutput);
        $this->assertStringContainsString('Row(Property) 1', $actualOutput);
        $this->assertStringContainsString('Number of Owners: 2', $actualOutput);
        $this->assertStringContainsString('Title: Mr', $actualOutput);
        $this->assertStringContainsString('First Name: John', $actualOutput);
        $this->assertStringContainsString('Row(Property) 2', $actualOutput);
        $this->assertStringContainsString('All data exported successfully!', $actualOutput);
    }
}
