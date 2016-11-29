<?php


namespace Benwilkins\Analyst\Clients\Internal\Metrics;


use Benwilkins\Analyst\AnalystDataCollection;
use Benwilkins\Analyst\Period;
use Carbon\Carbon;

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

    protected function isGolden(Period $period)
    {
        $now = Carbon::now();

        return $now->gt(Carbon::instance($period->end));
    }
}