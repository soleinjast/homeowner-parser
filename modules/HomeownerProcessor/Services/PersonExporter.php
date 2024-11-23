<?php

namespace Modules\HomeownerProcessor\Services;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PersonExporter implements PersonExporterInterface
{
    protected SymfonyStyle $io;

    public function __construct(InputInterface $input = null, OutputInterface $output = null)
    {
        $output = $output ?: new \Symfony\Component\Console\Output\ConsoleOutput();
        $input = $input ?: new \Symfony\Component\Console\Input\ArgvInput();
        $this->io = new SymfonyStyle($input, $output);
    }

    public function exportToJson(array $propertiesWithOwners): void
    {
        $this->io->title('Exported Homeowner Data by Row');

        foreach ($propertiesWithOwners as $propertyRow => $owners) {
            $this->io->section($propertyRow);
            $this->io->text(sprintf('<fg=cyan>Number of Owners:</> %d', count($owners)));
            foreach ($owners as $index => $owner) {
                $this->io->text(sprintf('<fg=yellow>Owner %d:</>', $index + 1));
                $this->io->listing([
                    sprintf('<fg=green>Title:</> %s', $owner['title'] ?? 'N/A'),
                    sprintf('<fg=cyan>First Name:</> %s', $owner['first_name'] ?? 'N/A'),
                    sprintf('<fg=yellow>Initials:</> %s', $owner['initial'] ?? 'N/A'),
                    sprintf('<fg=magenta>Last Name:</> %s', $owner['last_name'] ?? 'N/A'),
                ]);
            }
        }

        $this->io->success('All data exported successfully!');
    }
}
