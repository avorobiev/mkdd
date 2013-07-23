<?php

namespace Maxposter\DacBundle\Exceptions;

class DacMappingException extends \RuntimeException
{

    public function __construct($message = "Фильтрация по составным полям для Dac не проводится.")
    {
        parent::__construct($message);
    }
}