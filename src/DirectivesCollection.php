<?php

namespace Er1z\MarlinConfigConverter;

/**
 * @template-extends \ArrayObject<string, string>
 */
class DirectivesCollection extends \ArrayObject
{
    public function diff(self $other): self
    {
        return new self(array_diff_assoc($this->getArrayCopy(), $other->getArrayCopy()));
    }
}
