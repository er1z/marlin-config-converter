<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$kernel = new Er1z\MarlinConfigConverter\Symfony\Kernel('prod', false);
$kernel->boot();

exit($application->run());
