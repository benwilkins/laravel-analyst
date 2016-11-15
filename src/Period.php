<?php


namespace Benwilkins\Analyst;


use Benwilkins\Analyst\Exceptions\InvalidPeriod;
use Carbon\Carbon;

/**
 * Class Period
 * @package Benwilkins\Analyst
 */
class Period
{
    /**
     * @var \DateTime
     */
    public $start;
    /**
     * @var \DateTime
     */
    public $end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
        if ($start > $end) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($start, $end);
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Creates a period from specified start and end dates.
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @return static
     */
    public static function create(\DateTime $start, \DateTime $end)
    {
        return new static($start, $end);
    }

    /**
     * Creates a period with the end date as today, and the start
     * date as the specified number of days ago.
     *
     * @param int $numberOfDays
     * @return static
     */
    public static function days($numberOfDays)
    {
        $end = Carbon::today();
        $start = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($start, $end);
    }

    /**
     * Creates an array of Carbon instances at each interval step
     * between the start and end dates.
     *
     * @param int $step
     * @return array
     */
    public function interval($step = 1)
    {
        $start = Carbon::instance($this->start);
        $end = Carbon::instance($this->end);
        $intervals = [$start];

        if ($start->isSameDay($end)) {
            return $intervals;
        }

        while ($start->addDays($step)->lte($end)) {
            array_push($intervals, $start->copy());
        }

        return $intervals;
    }
}