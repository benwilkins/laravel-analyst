<?php


namespace Benwilkins\Analyst\Tests;


use Benwilkins\Analyst\Clients\Internal\Client;
use Benwilkins\Analyst\Clients\Internal\Metrics\NewUsersMetric;
use Benwilkins\Analyst\Period;

class InternalClientTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateMetric()
    {
        $client = new Client();

        $this->assertInstanceOf(NewUsersMetric::class, $client->instantiateMetric('new-users'));
        $this->assertInstanceOf(NewUsersMetric::class, $client->instantiateMetric('new users'));
        $this->assertInstanceOf(NewUsersMetric::class, $client->instantiateMetric('newUsers'));
        $this->assertInstanceOf(NewUsersMetric::class, $client->instantiateMetric('NewUsers'));
    }
}
