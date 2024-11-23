<?php

namespace Modules\HomeownerProcessor\Tests\Unit;
use Modules\HomeownerProcessor\Commands\ProcessHomeownersCommand;
use Modules\HomeownerProcessor\Core\HomeownerProcessor;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

class ProcessHomeownersCommandTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCommandHandlesSuccess(): void
    {
        // Mock the HomeownerProcessor to simulate a successful process
        $processor = $this->createMock(HomeownerProcessor::class);
        $processor->expects($this->once())->method('process');

        // Set up a buffered output and an array input for testing
        $output = new BufferedOutput();
        $input = new ArrayInput(['file' => 'valid.csv']);

        // Instantiate the command with the mocked processor
        $command = new ProcessHomeownersCommand($processor);
        $command->setLaravel(app());
        $command->run($input, $output);

        // Assert the success message is included in the output
        $actualOutput = $output->fetch();
        $this->assertStringContainsString('Homeowner data processed successfully!', $actualOutput);
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
}
