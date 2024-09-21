#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__."/vendor/autoload.php";


use App\Command\AddCommand;
use Symfony\Component\Console\Application;

$application = new Application('Expense tracker', '1.0.0');

$application->add(new AddCommand());



$application->run();
