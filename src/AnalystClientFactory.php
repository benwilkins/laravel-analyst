<?php


namespace Benwilkins\Analyst;


use Benwilkins\Analyst\Clients\AnalystClientInterface;
use Benwilkins\Analyst\Exceptions\ClientNotFoundException;

/**
 * Class AnalystClientFactory
 * @package Benwilkins\Analyst
 */
class AnalystClientFactory
{
    const CLIENT_NAMESPACE = '\Benwilkins\Analyst\Clients\\';

    /**
     * Instantiates an instance of the requested client.
     *
     * @param $name
     * @param array $args
     * @return AnalystClientInterface|object
     */
    public static function createClient($name, $args = [])
    {
        $class = self::CLIENT_NAMESPACE . $name . '\Client';

        self::validateClass($class);

        $reflector = new \ReflectionClass($class);

        return $reflector->newInstanceArgs($args);
    }

    /**
     * @param $class
     * @throws ClientNotFoundException
     */
    private static function validateClass($class)
    {
        if (empty($class)) {
            throw ClientNotFoundException::nameNotProvided();
        }

        if (!class_exists($class)) {
            throw ClientNotFoundException::clientNotFound($class);
        }
    }
}