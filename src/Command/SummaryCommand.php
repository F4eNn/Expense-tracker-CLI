<?php

namespace App\Command;

use App\Enums\CMD_options;
use App\Services\ExpensesStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'summary',
    hidden: false,
    description: "Total expenses."
)]
class SummaryCommand extends Command
{
    protected function configure()
    {
        $this
            ->addOption(CMD_options::Month->value, null, InputOption::VALUE_OPTIONAL, "Total expenses for selected month.");
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storage = new ExpensesStorage();
        $month = $input->getOption(CMD_options::Month->value);

        if ($month) {
            if (!is_numeric($month)) {
                $output->writeln("<error>Month must be an integer.</error>");
                return Command::FAILURE;
            }
            if ($month < 1 || $month > 12) {
                $output->writeln("<error>Invalid month.</error>");
                return Command::FAILURE;
            }
        }

        $summary  = $storage->summaryExpenses($month);
        $output->writeln("<info>$summary</info>");

        return Command::SUCCESS;
    }
}
