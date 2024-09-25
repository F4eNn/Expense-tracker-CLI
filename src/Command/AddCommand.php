<?php

namespace App\Command;

use App\Enums\CMD_options;
use App\Services\ExpensesStorage;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;


#[AsCommand(
    name: 'add',
    description: "Add expenses.",
    aliases: ['a'],
    hidden: false,
)]
class AddCommand extends SymfonyCommand
{


    protected function configure()
    {
        $this
            ->addOption(CMD_options::Description->value, null, InputOption::VALUE_REQUIRED, "Description of the expense.")
            ->addOption(CMD_options::Amount->value, null, InputOption::VALUE_REQUIRED, "Amount of the expense.")
            ->addOption(CMD_options::Category->value, null, InputOption::VALUE_REQUIRED, "Category of the expense.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException("This command accepts only an instance of 'ConsoleOutputInterface'.");
        }
        var_dump(CMD_options::Description->value);

        $data = [
            "description" => $this->validateInput($input->getoption(CMD_options::Description->value), CMD_options::Description->value),
            "amount" => $this->validateInput($input->getOption(CMD_options::Amount->value), CMD_options::Amount->value, true),
            "category" => $this->validateInput($input->getOption(CMD_options::Category->value), CMD_options::Category->value)
        ];

        $storage = new ExpensesStorage();
        $storage->addNewExpense($data);

        return SymfonyCommand::SUCCESS;
    }

    private function validateInput(string | null  $input, string $name, bool $isNumeric = false)
    {
        if (!$input || empty(trim($input))) {
            echo "\e[31m" . ucfirst($name) . " is required.\e[0m";
            exit;
        }
        if ($isNumeric && !is_numeric($input)) {
            echo "\e[31m" . ucfirst($name) . " must be an integer.";
            exit;
        }
        if ($isNumeric && (int) $input < 0) {
            echo "\e[31m" . ucfirst($name) . " Can't be negative.\e[0m";
            exit;
        }
        return $input;
    }
}
