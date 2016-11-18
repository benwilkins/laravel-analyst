<?php


namespace Benwilkins\Analyst\Exceptions;

use Exception;

class InvalidMetricException extends Exception
{
    public static function MetricNotFound($name)
    {
        return new static('Could not find metric by name (' . $name . ').');
    }

    public static function GoogleViewIdNotProvided()
    {
        return new static('A view ID must be provided.');
    }

    public static function GoogleAuthFileNotFound()
    {
        return new static('Could not find the Google service account JSON file.');
    }
}