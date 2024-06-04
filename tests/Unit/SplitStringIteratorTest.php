<?php

namespace Tests\Er1z\MarlinConfigConverter\Unit;

use Er1z\MarlinConfigConverter\SplitStringIterator;
use PHPUnit\Framework\TestCase;

class SplitStringIteratorTest extends TestCase
{
    public function testIfStringIsSplit(): void
    {
        $result = new SplitStringIterator('first;test;line', ';');

        self::assertEquals(['first', 'test', 'line'], iterator_to_array($result));
    }
}
