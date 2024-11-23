<?php

namespace Modules\HomeownerProcessor\Tests\E2E;
use Tests\TestCase;
class ProcessHomeownersCommandTest extends TestCase
{
    public function testProcessHomeownersCommandWithValidFile(): void
    {

        $filePath = storage_path('app/test_homeowners.csv');
        file_put_contents($filePath, "Name\nDr. John Doe & Mr and Mrs Smith\nMiss Emily Brown and Prof. Michael Green");

        $this->artisan('homeowners:process', ['file' => $filePath])
            ->assertExitCode(0); // passed with no error!

        unlink($filePath);
    }
}
