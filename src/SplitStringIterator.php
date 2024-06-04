<?php

namespace Er1z\MarlinConfigConverter;

/**
 * @template-implements  \IteratorAggregate<int, string>
 */
readonly class SplitStringIterator implements \IteratorAggregate
{
    public function __construct(
        private string $payload,
        private string $delimiter
    ) {
    }

    public function getIterator(): \Traversable
    {
        $current = 0;
        $length = strlen($this->payload);
        $delimiterLength = strlen($this->delimiter);

        while ($current < $length) {
            $delimiterPos = strpos($this->payload, $this->delimiter, $current);
            if (false === $delimiterPos) {
                $delimiterPos = $length;
            }

            yield substr($this->payload, $current, $delimiterPos - $current);

            $current += ($delimiterPos - $current) + $delimiterLength;
        }
    }
}
