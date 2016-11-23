<?php


namespace Benwilkins\Analyst;


/**
 * Class AnalystDataCollection
 * @package Benwilkins\Analyst
 */
class AnalystDataCollection
{
    /**
     * @var Period
     */
    public $period;
    /**
     * @var int
     */
    public $total;
    /**
     * @var array
     */
    public $groups = [];
    /**
     * @var mixed
     */
    public $raw;

    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param Period $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

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
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param AnalystDataGroup $group
     */
    public function addGroup(AnalystDataGroup $group)
    {
        if ($handle = $group->getGroupHandle()) {
            $this->groups[$handle] = $group;

        } else {
            array_push($this->groups, $group);
        }
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }
}