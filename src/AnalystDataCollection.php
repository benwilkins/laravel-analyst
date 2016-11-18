<?php


namespace Benwilkins\Analyst;


/**
 * Class AnalystDataCollection
 * @package Benwilkins\Analyst
 */
class AnalystDataCollection
{
    /**
     * @var int
     */
    protected $total;
    /**
     * @var array
     */
    protected $groups = [];
    /**
     * @var mixed
     */
    protected $raw;

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