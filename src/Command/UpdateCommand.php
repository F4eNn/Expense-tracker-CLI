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
    name: "update",
    hidden: false,
    description: "Update and expense"
)]
class UpdateCommand extends Command
{
    protected function configure()
    {

        $this
            ->addOption(CMD_options::Description->value, null, InputOption::VALUE_OPTIONAL, 'Change description of the epxenses')
            ->addOption(CMD_options::Amount->value, null, InputOption::VALUE_OPTIONAL, 'Change amount of the expenses')
            ->addOption(CMD_options::Category->value, null, InputOption::VALUE_OPTIONAL)
            ->addOption(CMD_options::ID->value, null, InputOption::VALUE_REQUIRED, 'ID of the expenses');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption(CMD_options::ID->value);
        $description = $input->getOption('description');
        $amount = $input->getOption('amount');
        $category = $input->getOption('category');

        if (is_string($description) && empty(trim($description))) {
            echo "\e[31m Description can't be empty.";
            exit;
        }
        if (is_string($category) && empty(trim($category))) {
            echo "\e[31m Category can't be empty.";
            exit;
        }
        if (is_string($amount) && empty(trim($category))) {
            echo "\e[31m Amount can't be empty.";
            exit;
        }
        if (!is_numeric($amount) || (int)$amount < 0) {
            echo "\e[31m Amount is Invalid.";
            exit;
        }
        if (!$id || !is_numeric($id) || $id < 0) {
            $output->writeln('<error>ID is invalid.</error>');
            return Command::FAILURE;
        }
        $data_to_update = [
            CMD_options::Description->value => $description,
            CMD_options::Amount->value => "$" . $amount,
            CMD_options::Category->value => $category,
            CMD_options::ID->value => (int)$input->getOption(CMD_options::ID->value)
        ];
        $storage = new ExpensesStorage();
        $res = $storage->updateExpenses((int)$id, $data_to_update);
        if (!$res) {
            echo "\e[31m Not found any resources";
            exit;
        } else {
            echo "\e[32m Succesffully updated expense (ID:$id)";
        }
        return Command::SUCCESS;
    }
}
