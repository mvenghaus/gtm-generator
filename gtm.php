<?php

define('ROOT_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/di.php';
$app = $container->get(Symfony\Component\Console\Application::class);

$app->add($container->get(Command\GenerateCommand::class));

$app->run();