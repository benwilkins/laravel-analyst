<?php


namespace Benwilkins\Analyst\Clients\Internal\Metrics;


use Benwilkins\Analyst\Period;

abstract class Metric
{
    /**
     * Gets the data for a metric.
     *
     * @param Period $period
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    abstract public function run(Period $period, $params = []);
}