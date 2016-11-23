<?php


namespace Benwilkins\Analyst;


/**
 * Class AnalystDataGroup
 * @package Benwilkins\Analyst
 */
class AnalystDataGroup
{
    /**
     * @var int
     */
    public $total = 0;
    /**
     * @var array
     */
    public $points = [];
    /**
     * @var string
     */
    public $groupHandle;

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return array
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param array $points
     */
    public function setPoints(array $points)
    {
        $this->points = $points;
    }

    /**
     * @param array $point
     */
    public function addDataPoint(array $point)
    {
        array_push($this->points, $point);
    }

    /**
     * @return string
     */
    public function getGroupHandle()
    {
        return $this->groupHandle;
    }

    /**
     * @param string $groupHandle
     */
    public function setGroupHandle($groupHandle)
    {
        $this->groupHandle = $groupHandle;
    }
}