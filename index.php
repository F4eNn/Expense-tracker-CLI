#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";


use App\Command\AddCommand;
use App\Command\BudgetCommand;
use App\Command\ListCommand;
use App\Command\SummaryCommand;
use App\Command\DeleteCommand;
use App\Command\ExportToCSVCommand;
use App\Command\UpdateCommand;




use Symfony\Component\Console\Application;

$application = new Application('Expense tracker', '1.0.0');

$application->add(new AddCommand());
$application->add(new ListCommand());
$application->add(new SummaryCommand());
$application->add(new DeleteCommand());
$application->add(new UpdateCommand());
$application->add(new BudgetCommand());
$application->add(new ExportToCSVCommand());


$application->run();
