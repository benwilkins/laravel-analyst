<?php


namespace Benwilkins\Analyst\Tests;


use Benwilkins\Analyst\AnalystClientFactory;

class AnalystClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnalystClientFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new AnalystClientFactory();
    }

    public function testCanCreateClassInstanceFromFullName()
    {
        $instance = $this->factory->createClient('Internal');

        $this->assertInstanceOf('\Benwilkins\Analyst\Clients\AnalystClientInterface', $instance);
        $this->assertInstanceOf('\Benwilkins\Analyst\Clients\Internal\Client', $instance);
    }

    /**
     * @expectedException \Benwilkins\Analyst\Exceptions\ClientNotFoundException
     */
    public function testNonExistentClassThrowsException()
    {
        $this->factory->createClient('FooBar');
    }
}
