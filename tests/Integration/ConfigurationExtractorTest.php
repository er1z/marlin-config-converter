<?php

namespace Tests\Er1z\MarlinConfigConverter\Integration;

use Er1z\MarlinConfigConverter\ConfigurationExtractor;
use Er1z\MarlinConfigConverter\Exception\EmptyConfigurationException;
use Er1z\MarlinConfigConverter\Exception\PreprocessorNotCallableException;
use PHPUnit\Framework\TestCase;

class ConfigurationExtractorTest extends TestCase
{
    public function testCppUnavailable(): void
    {
        self::expectException(PreprocessorNotCallableException::class);
        (new ConfigurationExtractor('szczebrzeszyn'))('', '');
    }

    public function testForValid(): void
    {
        $result = (new ConfigurationExtractor())(
            sprintf('%s/../stubs/extractor', __DIR__),
            sprintf('%s/../stubs/extractor/conf.h', __DIR__),
            sprintf('%s/../stubs/extractor/conf_adv.h', __DIR__),
        );

        self::assertStringContainsString('#define ADVANCED_PROPERTY 1', $result);
        self::assertStringContainsString('#define BASIC_PROPERTY 1', $result);
    }

    public function testForEmpty(): void
    {
        self::expectException(EmptyConfigurationException::class);
        (new ConfigurationExtractor('true'))(
            sprintf('%s/../stubs/extractor', __DIR__),
            sprintf('%s/../stubs/extractor/conf.h', __DIR__),
            sprintf('%s/../stubs/extractor/conf_adv.h', __DIR__),
        );
    }
}
