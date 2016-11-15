<?php


namespace Benwilkins\Analyst\Exceptions;

use Exception;

class InvalidMetricException extends Exception
{
    public static function MetricNotFound($name)
    {
        return new static('Could not find metric by name (' . $name . ').');
    }
}