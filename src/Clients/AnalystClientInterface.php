<?php


namespace Benwilkins\Analyst\Clients;


use Benwilkins\Analyst\Period;

interface AnalystClientInterface
{
    /**
     * Gets the data for a specified internal metric.
     *
     * @param string $metricName
     * @param Period $period
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getMetric($metricName, Period $period, $params = []);


}