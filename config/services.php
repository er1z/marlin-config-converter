<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Eriz\\MarlinConfigConverter\\', '../src/')
        ->exclude('../src/{Kernel.php}');

    $services
        ->set(Symfony\Component\Serializer\Encoder\EncoderInterface::class, Er1z\MarlinConfigConverter\Symfony\IniEncoder::class)
    ;

    $services
        ->get(Er1z\MarlinConfigConverter\Command\ConvertCommand::class)
        ->public()
    ;
};
