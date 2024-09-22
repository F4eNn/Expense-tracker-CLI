<?php

namespace App\Command;


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
    private const DESCRIPTION = "description";
    private const SHORT_DESRIPTION = 'd';
    private const AMOUNT = "amount";
    private const SHORT_AMOUNT = 'a';
    private const CATEGORY = 'category';
    private const SHORT_CATEGORY = 'c';

    protected function configure()
    {
        $this
            ->addOption(self::DESCRIPTION, self::SHORT_DESRIPTION, InputOption::VALUE_REQUIRED, "Description of the expense.")
            ->addOption(self::AMOUNT, self::SHORT_AMOUNT, InputOption::VALUE_REQUIRED, "Amount of the expense.")
            ->addOption(self::CATEGORY, self::SHORT_CATEGORY, InputOption::VALUE_REQUIRED, "Category of the expense.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException("This command accepts only an instance of 'ConsoleOutputInterface'.");
        }


        $data = [
            "description" => $this->validateInput($input->getoption(self::DESCRIPTION), self::DESCRIPTION),
            "amount" => $this->validateInput($input->getOption(self::AMOUNT), self::AMOUNT, true),
            "category" => $this->validateInput($input->getOption(self::CATEGORY), self::CATEGORY)
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
