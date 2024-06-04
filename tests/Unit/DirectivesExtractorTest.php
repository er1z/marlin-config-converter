<?php

namespace Tests\Er1z\MarlinConfigConverter\Unit;

use Er1z\MarlinConfigConverter\DirectivesExtractor;
use PHPUnit\Framework\TestCase;

class DirectivesExtractorTest extends TestCase
{
    public function testExtraction(): void
    {
        $configuration = <<<CONFIGURATION
#define KEY value
#define VALUELESS
#define _IGNORED something
#define BOARD_BLAH my-board
#define SOME_FUNC(x,y) BLAH(x)
rubbish
#define AUX_X1 my-aux
CONFIGURATION;

        $result = (new DirectivesExtractor())($configuration);

        self::assertSame([
            'KEY' => 'value',
            'VALUELESS' => 'on',
        ], $result->getArrayCopy());
    }
}
