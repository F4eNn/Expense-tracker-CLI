<?php

namespace App\Command;

use App\Enums\CMD_options;
use App\Services\ExpensesStorage;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'all',
    description: 'View all expenses.',
    hidden: false,
)]
class ListCommand extends Command
{

    protected function configure(): void
    {
        $this->addOption(CMD_options::Category->value, null, InputOption::VALUE_OPTIONAL, 'Sort by category');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $storage = new ExpensesStorage();
        $category = $input->getOption(CMD_options::Category->value);
        $expenses = $storage->listExpenses($category);

        $table = new Table($output);
        $table->setHeaderTitle('Expenses');
        echo "\n";
        $table
            ->setHeaders(['ID', 'Date', "Description", "Amount", "Category"])
            ->setRows($expenses);

        $table->render();

        return Command::SUCCESS;
    }
}
