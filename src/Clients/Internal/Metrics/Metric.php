<?php


namespace Benwilkins\Analyst\Clients\Internal\Metrics;


use Benwilkins\Analyst\AnalystDataCollection;
use Benwilkins\Analyst\Period;

abstract class Metric
{
    /**
     * Gets the data for a metric.
     *
     * @param Period $period
     * @param array $params
     * @return AnalystDataCollection
     */
    abstract public function run(Period $period, $params = []);
}