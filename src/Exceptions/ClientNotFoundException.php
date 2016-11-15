<?php

namespace Benwilkins\Analyst\Exceptions;

use Exception;

class ClientNotFoundException extends Exception
{
    public static function nameNotProvided()
    {
        return new static('Could not create an instance of the client because no name was provided.');
    }

    public static function clientNotFound($name)
    {
        return new static('Could not find a client by the specified name (' . $name . ').');
    }
}