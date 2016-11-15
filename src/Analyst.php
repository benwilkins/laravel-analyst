<?php


namespace Benwilkins\Analyst;


use Benwilkins\Analyst\Clients\AnalystClientInterface;
use Illuminate\Support\Collection;

/**
 * Class Analyst
 * @package Benwilkins\Analyst
 */
class Analyst
{
    /**
     * Creates an instance of the requested client.
     *
     * @param string $name
     * @param array $args
     * @return AnalystClientInterface
     */
    public function client($name, $args = [])
    {
        return AnalystClientFactory::createClient(ucfirst($name), $args);
    }

    /**
     * Gets data for a given metric on a specified client. If no client is
     * specified, the default client will be used. The default client can
     * be set from the config.
     *
     * @param $metricName
     * @param Period $period
     * @param AnalystClientInterface|null $client
     * @param array $params
     * @return Collection
     */
    public function metric($metricName, Period $period, AnalystClientInterface $client = null, $params = [])
    {
        if (is_null($client)) {
            $client = $this->client(config('laravel-analyst.default_client', 'internal'));
        }

        return $client->getMetric($metricName, $period, $params);
    }

    /**
     * @param array $metrics
     * @param $chartType
     */
    public function report(array $metrics, $chartType)
    {
        // TODO
    }
}