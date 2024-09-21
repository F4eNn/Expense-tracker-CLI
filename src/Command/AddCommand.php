<?php

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;


#[AsCommand(
    name: 'add',
    description: "Add expenses.",
    aliases: ['add', 'a'],
    hidden: false,
)]
class AddCommand extends Command
{
    private const DESCRIPTION = "description";
    private const SHORT_DESRIPTION = 'desc';
    private const AMOUNT = "amount";
    private const SHORT_AMOUNT = 'a';

    protected function configure()
    {
        $this
            ->addOption(self::DESCRIPTION, self::SHORT_DESRIPTION, InputOption::VALUE_REQUIRED, "Description of the expense.")
            ->addOption(self::AMOUNT, self::SHORT_AMOUNT, InputOption::VALUE_REQUIRED, "Amount of the expense.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException("This command accepts only an instance of 'ConsoleOutputInterface'.");
        }
        $description = $this->validateInput($input->getoption(self::DESCRIPTION), self::DESCRIPTION);
        $amount = $this->validateInput($input->getOption(self::AMOUNT), self::AMOUNT);

        echo "$description, $amount";

        return Command::SUCCESS;
    }

    private function validateInput(string | null  $input, string $name)
    {
        if (!$input || empty(trim($input))) {
            echo "\e[31m".ucfirst($name) . " is required.\e[0m";
            exit;
        }
        if(is_numeric($input) && (int) $input < 0){
            echo "\e[31m".ucfirst($name) . " Can't be negative.\e[0m";
            exit;
        }
        return $input;
    }
}
