<?php


namespace Benwilkins\Analyst\Clients\Google;


use Benwilkins\Analyst\AnalystDataCollection;
use Benwilkins\Analyst\AnalystDataGroup;
use Benwilkins\Analyst\Clients\AnalystClientInterface;
use Benwilkins\Analyst\Exceptions\InvalidMetricException;
use Benwilkins\Analyst\Period;

class Client implements AnalystClientInterface
{
    /**
     * @var \Google_Service_AnalyticsReporting
     */
    protected $analytics;
    /**
     * @var string
     */
    protected $viewId;
    /**
     * @var array [\Google_Service_AnalyticsReporting_DateRange]
     */
    protected $dateRanges = [];
    /**
     * @var array [\Google_Service_AnalyticsReporting_Metric]
     */
    protected $metrics = [];
    /**
     * @var \Google_Service_AnalyticsReporting_ReportRequest
     */
    protected $request;
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @inheritdoc
     */
    public function getMetric($metricName, Period $period, $params = [])
    {
        $this->initializeAnalytics();

        $data = $this->formatReportResult($this->getReport($metricName, $period, $params), $params);
        $data->setPeriod($period);

        return $data;
    }

    /**
     * @param $metricName
     * @param Period $period
     * @param $params
     * @return \Google_Service_AnalyticsReporting_GetReportsResponse
     */
    protected function getReport($metricName, Period $period, $params)
    {
        $this->getViewId($params);
        $this->generateDateRange($period);
        $this->generateMetrics($metricName, $params);
        $this->generateRequest($params);

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$this->request]);

        return $this->analytics->reports->batchGet($body);
    }

    /**
     * @throws InvalidMetricException
     */
    protected function initializeAnalytics()
    {
        $authFilePath = config('laravel-analyst.google_account_credentials_json');

        if (!file_exists($authFilePath)) {
            throw InvalidMetricException::GoogleAuthFileNotFound();
        }

        $client = new \Google_Client();
        $client->setApplicationName('CaredFor Analytics');
        $client->setAuthConfig($authFilePath);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        $this->analytics = new \Google_Service_AnalyticsReporting($client);
    }

    /**
     * @param $params
     * @throws InvalidMetricException
     */
    protected function getViewId($params)
    {
        if (array_key_exists('viewId', $params) && !empty($params['viewId'])) {
            $this->viewId = $params['viewId'];

            return;
        }

        if ($viewId = config('laravel-analyst.default_view_id', false)) {
            $this->viewId = $viewId;

            return;
        }

        throw InvalidMetricException::GoogleViewIdNotProvided();
    }

    /**
     * @param string $metricExpression
     * @param array $params
     */
    protected function generateMetrics($metricExpression, $params)
    {
        if (! is_array($metricExpression)) {
            $metricExpression = [$metricExpression];
        }

        foreach ($metricExpression as $index => $expression) {
            $metric = new \Google_Service_AnalyticsReporting_Metric();
            $metric->setExpression($expression);

            if (key_exists('alias', $params) && is_array($params['alias']) && $params['alias'][$index]) {
                $metric->setAlias($params['alias'][$index]);
            }

            array_push($this->metrics, $metric);
        }
    }

    /**
     * @param Period $period
     */
    protected function generateDateRange(Period $period)
    {
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setEndDate($period->end->format('Y-m-d'));
        $dateRange->setStartDate($period->start->format('Y-m-d'));

        array_push($this->dateRanges, $dateRange);
    }

    /**
     *
     */
    protected function generateRequest($params)
    {
        $this->request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $this->request->setViewId($this->viewId);
        $this->request->setDateRanges($this->dateRanges);
        $this->request->setMetrics($this->metrics);

        if (key_exists('dimensions', $params) && !empty($params['dimensions'])) {
            $dimensions = [];

            foreach ($params['dimensions'] as $dimensionExpression) {
                $dimension = new \Google_Service_AnalyticsReporting_Dimension();
                $dimension->setName($dimensionExpression);

                array_push($dimensions, $dimension);
            }

            $this->request->setDimensions($dimensions);
        }
    }

    protected function formatReportResult(\Google_Service_AnalyticsReporting_GetReportsResponse $response, array $params)
    {
        $raw = clone $response;
        $formattedData = new AnalystDataCollection();

        /** @var \Google_Service_AnalyticsReporting_Report $report */
        $report = $response->getReports()[0];
        /** @var \Google_Service_AnalyticsReporting_ColumnHeader $header */
        $header = $report->getColumnHeader();
        $dimensionHeader = $header->getDimensions();
//        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();

        /** @var \Google_Service_AnalyticsReporting_ReportData $responseData */
        $responseData = $report->getData();

        $formattedData->setTotal($responseData->getTotals()[0]->getValues()[0]);

        $groups = ($this->shouldGroupByDimensions($params))
            ? $this->createDataGroupsForDimensions($responseData->getRows(), $params['groupByDimensions'], $dimensionHeader)
            : [$this->createDataGroupFromRows($responseData->getRows(), $dimensionHeader)];

        foreach ($groups as $group) {
            $formattedData->addGroup($group);
        }

        $formattedData->setRaw(collect($raw));

        return $formattedData;
    }

    /**
     * @param array $params
     * @return bool
     */
    protected function shouldGroupByDimensions(array $params)
    {
        return key_exists('groupByDimensions', $params) && $params['groupByDimensions'];
    }

    /**
     * @param $rows
     * @param $dimensionHeader
     * @return AnalystDataGroup
     */
    protected function createDataGroupFromRows($rows, $dimensionHeader)
    {
        $group = new AnalystDataGroup();
        $total = 0;

        $group->addDataPoint($this->createDataPointColumnsFromDimensions($dimensionHeader));

        /** @var \Google_Service_AnalyticsReporting_ReportRow $row */
        foreach ($rows as $row) {
            $value = $row->getMetrics()[0]->getValues()[0];
            $point = $row->getDimensions();

            array_push($point, $value);
            $group->addDataPoint($point);

            $total += $value;
        }

        $group->setTotal($total);

        return $group;
    }

    protected function createDataGroupsForDimensions($rows, $groupByDimensions, $dimensionHeader)
    {
        $groups = [];

        /** @var \Google_Service_AnalyticsReporting_ReportRow $row */
        foreach ($rows as $row) {
            $dimensions = $row->getDimensions();
            $value = $row->getMetrics()[0]->getValues()[0];
            $groupHandle = $this->getGroupHandleFromDimensions($groupByDimensions, $dimensions);

            if (!key_exists($groupHandle, $groups)) {
                $group = new AnalystDataGroup();

                $group->setGroupHandle($groupHandle);
                $group->addDataPoint($this->createDataPointColumnsFromDimensions($dimensionHeader, $groupByDimensions));

                $groups[$groupHandle] = $group;
            }

            $groups[$groupHandle]->setTotal($groups[$groupHandle]->getTotal() + $value);

            $point = [];

            foreach ($dimensions as $index => $dimension) {
                if (! in_array($index, $groupByDimensions)) {
                    array_push($point, $dimension);
                }
            }

            array_push($point, $value);
            $groups[$groupHandle]->addDataPoint($point);
        }

        return $groups;
    }

    /**
     * @param array $dimensionHeader
     * @param array $groupByDimensions
     * @return array
     */
    protected function createDataPointColumnsFromDimensions($dimensionHeader, $groupByDimensions = [])
    {
        $columns = [];

        foreach ($dimensionHeader as $index => $dimensionName) {
            if (!in_array($index, $groupByDimensions)) {
                array_push($columns, $this->formatDimensionName($dimensionName));
            }
        }

        array_push($columns, 'count');

        return $columns;
    }

    /**
     * @param $dimension
     * @return string
     */
    protected function formatDimensionName($dimension)
    {
        return (strpos($dimension, 'ga:') !== false)
            ? substr($dimension, 3)
            : $dimension;
    }

    /**
     * @param $groupByDimensions
     * @param $dimensions
     * @return string
     */
    protected function getGroupHandleFromDimensions($groupByDimensions, $dimensions)
    {
        $groupHandle = '';

        foreach ($groupByDimensions as $dimensionIndex) {
            $groupHandle .= ($groupHandle !== '') ? ' ' : '';
            $groupHandle .= $dimensions[$dimensionIndex];
        }

        return $groupHandle;
    }
}