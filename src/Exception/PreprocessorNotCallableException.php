<?php

namespace Er1z\MarlinConfigConverter\Exception;

class PreprocessorNotCallableException extends \LogicException
{
    public function __construct()
    {
        parent::__construct('Cannot find `cpp` within PATH. Have you installed compiler chain?');
    }
}
