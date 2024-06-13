<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$env = getenv('APP_ENV');
$kernel = new Er1z\MarlinConfigConverter\Symfony\Kernel($env ?: 'prod', $env === 'dev');
$kernel->boot();

$container = $kernel->getContainer();
$command = $container->get(Er1z\MarlinConfigConverter\Command\ConvertCommand::class);
$application->add($command);
$application->setDefaultCommand('convert', true);
exit($application->run());
