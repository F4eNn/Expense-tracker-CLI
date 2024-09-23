<?php

namespace App\Command;

use App\Services\ExpensesStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: "update",
    hidden: false,
    description: "Update and expense"
)]
class UpdateCommand extends Command 
{
    protected function configure()
    {

        $this
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Change description of the epxenses')
            ->addOption('amount', 'a', InputOption::VALUE_OPTIONAL, 'Change amount of the expenses')
            ->addOption("category", 'c', InputOption::VALUE_OPTIONAL)
            ->addOption("id", null, InputOption::VALUE_REQUIRED, 'ID of the expenses');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption("id");
        $data_to_update = [
            "description" => $input->getOption('description'),
            "amount" => $input->getOption("amount"),
            "category" => $input->getOption("category"),
            "id" => $input->getOption("id")
        ];
        if(!$id || !is_numeric($id) || $id < 0) {
            $output->writeln('<error>ID is invalid.</error>');
        }
        $storage = new ExpensesStorage();
        $storage->updateExpenses($id, $data_to_update);
        return Command::SUCCESS;
    }
}
