<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'export',
    hidden: false,
    description: "Export expenses to csv file."
)]
class ExportToCSVCommand extends Command
{
    private $fileName = "data.json";
    private $csvFile = "budget.csv";

    protected function configure() {}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($this->fileName)) {
            echo "\e[31m Resource not found \e[0m";
            return Command::FAILURE;
        }
        $budget = file_get_contents($this->fileName);
        $budget_json = json_decode($budget, true);

        $file_pointer = fopen($this->csvFile, 'w');

        foreach ($budget_json as $i) {
            fputcsv($file_pointer, $i);
        }
        fclose($file_pointer);
        
        $output->writeln("<info>Budget successfully exported.</info>");
        return Command::SUCCESS;
    }
}
