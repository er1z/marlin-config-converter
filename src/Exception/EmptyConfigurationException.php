<?php

namespace Er1z\MarlinConfigConverter\Exception;

class EmptyConfigurationException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Specified directory has no configuration');
    }
}
