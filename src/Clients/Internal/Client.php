<?php


namespace Benwilkins\Analyst\Clients\Internal;


use Benwilkins\Analyst\Clients\AnalystClientInterface;
use Benwilkins\Analyst\Clients\Internal\Metrics\Metric;
use Benwilkins\Analyst\Exceptions\InvalidMetricException;
use Benwilkins\Analyst\Period;

/**
 * Class Client
 * @package Benwilkins\Analyst\Clients\Internal
 */
class Client implements AnalystClientInterface
{
    const METRIC_NAMESPACE = '\Benwilkins\Analyst\Clients\Internal\Metrics\\';

    /**
     * @inheritdoc
     */
    public function getMetric($metricName, Period $period, $params = [])
    {
        $metric = $this->instantiateMetric($metricName);

        return $metric->run($period, $params);
    }

    /**
     * Creates an instance of the metric object.
     *
     * @param $name
     * @return Metric|object
     */
    public function instantiateMetric($name)
    {
        $name = studly_case($name) . 'Metric';
        $class = self::METRIC_NAMESPACE . $name;

        if (class_exists($class)) {
            $reflector = new \ReflectionClass($class);

            return $reflector->newInstance();

        } else {
            $this->includeCustomMetric($name);

            $reflector = new \ReflectionClass($class);

            return $reflector->newInstance();
        }
    }

    /**
     * @param $name
     * @throws InvalidMetricException
     */
    private function includeCustomMetric($name)
    {
        $path = self::getCustomMetricPath();
        $file = $path.$name.'.php';

        if (file_exists($file)) {
            require_once($file);
        } else {
            throw InvalidMetricException::MetricNotFound($file);
        }
    }

    /**
     * Gets the path for custom metrics. This can be set from the config.
     *
     * @return string
     */
    public static function getCustomMetricPath()
    {
        return base_path() . config('laravel-analyst.custom_metric_location');
    }
}