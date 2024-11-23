<?php

namespace Modules\HomeownerProcessor\Commands;
use Exception;
use Illuminate\Console\Command;
use Modules\HomeownerProcessor\Core\HomeownerProcessor;
use RuntimeException;


class ProcessHomeownersCommand extends Command
{
    protected $signature = 'homeowners:process {file : The path to the CSV file}';
    protected $description = 'Process a CSV file containing homeowner data into structured records';
    public function __construct(protected HomeownerProcessor $processor)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $filePath = $this->argument('file');
        try {
            $this->processor->process($filePath);
            $this->info('Homeowner data processed successfully!');
        } catch (RuntimeException $e) {
            $this->error('Processing failed: ' . $e->getMessage());
            return 1;
        } catch (Exception $e) {
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            return 2;
        }
        return 0;
    }
}
