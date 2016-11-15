<?php


namespace Benwilkins\Analyst\Tests;


use Benwilkins\Analyst\Analyst;

class AnalystTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateClient()
    {
        $analyst = new Analyst();

        $client = $analyst->createClient('internal');

        $this->assertInstanceOf('\Benwilkins\Analyst\Clients\Internal\Client', $client);
    }
}
