<?php

namespace Er1z\MarlinConfigConverter\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function registerBundles(): iterable
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/../../config/services.php');
    }

    public function getProjectDir(): string
    {
        $isPhar = \Phar::running();
        return !empty($isPhar) ? $isPhar : __DIR__.'/../..';
    }
}
