<?php

namespace App\Command;

use App\Enums\CMD_options;
use App\Services\ExpensesStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'delete',
    hidden: false,
    description: "Delete an expense."
)]
class DeleteCommand extends Command
{
    protected function configure()
    {
        $this->addOption(CMD_options::ID->value, null, InputOption::VALUE_REQUIRED, 'Expenses id.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $id = $input->getOption(CMD_options::ID->value);

        if (!is_numeric($id) || $id < 0 || !$id) {
            $output->writeln("<error>ID is invalid.</error>");
            return Command::FAILURE;
        }
        $storage = new ExpensesStorage();
        $res = $storage->deleteExpense($id);

        if (!$res) {
            $output->writeln("<error>Not found any expenses to delete. </error>");
            return  Command::FAILURE;
        }
        $output->writeln("<info>Expense deleted successfully.</info>");
        return Command::SUCCESS;
    }
}
