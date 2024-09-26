<?php

namespace App\Command;


use App\Enums\CMD_options;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



#[AsCommand(
    name: "budget",
    description: "Set budget for each month.",
    hidden: false
)]
class BudgetCommand extends Command
{
    private $fileName = 'budget.json';



    protected function configure()
    {
        $this
            ->addOption(CMD_options::Limit->value, null, InputOption::VALUE_REQUIRED, 'Choose maximum amount of money to spend in this month')
            ->addOption(CMD_options::Month->value, null, InputOption::VALUE_REQUIRED, "Month budget");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $input->getOption(CMD_options::Limit->value);
        $month = $input->getOption(CMD_options::Month->value);

        if (!$limit) {
            $output->writeln("\e[31mLimit is required.\e[0m");
            return Command::FAILURE;
        }
        if (!is_numeric($limit) || $limit < 0) {
            $output->writeln("\e[31mLimit is Invalid.\e[0m");
            return Command::FAILURE;
        }
        if (!$month) {
            $output->writeln("\e[31mMonth is required.\e[0m");
            return Command::FAILURE;
        }
        if (!is_numeric($month) || $month > 12  || $month < 1) {
            $output->writeln("\e[31mMonth is Invalid.\e[0m");
            return Command::FAILURE;
        }

        if (!file_exists($this->fileName)) {
            return file_put_contents($this->fileName, json_encode([[CMD_options::Limit->value => "$" . $limit, CMD_options::Month->value => (int)$month]], JSON_PRETTY_PRINT));
            return Command::SUCCESS;
        }

        $curr_budget = json_decode(file_get_contents($this->fileName), true);

        $isFound = false;
        foreach ($curr_budget as &$item) {
            if ($item['month'] === (int)$month) {
                $item[CMD_options::Limit->value] = "$" . $limit;
                $isFound = true;
                break;
            }
        }
        if (!$isFound) {
            array_push($curr_budget, [CMD_options::Limit->value => "$" . $limit, CMD_options::Month->value => (int)$month]);
        }

        file_put_contents($this->fileName, json_encode($curr_budget, JSON_PRETTY_PRINT));

        $output->writeln("<info>Successfully updated your budget.</info>");
        return Command::SUCCESS;
    }

    public function getBudget(string $date, string $amount): void
    {
        $month = date("n", strtotime($date));

        if (!file_exists($this->fileName)) {
            return;
        }
        $budget = file_get_contents($this->fileName);
        $budget_json = json_decode($budget, true);

        foreach ($budget_json as $i) {
            if ((int)$i['month'] === (int)$month) {
                if ((int)explode('$', $i['limit'])[1] < (int)$amount) {
                    echo "\e[33m Warning, you exceeded the budget for this month.\n";
                    break;
                }
            }
        }
    }
}
