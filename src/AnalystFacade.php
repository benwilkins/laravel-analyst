<?php

namespace Benwilkins\Analyst;

use Illuminate\Support\Facades\Facade;

/**
 * Class AnalystFacade
 * @package Benwilkins\Analyst
 * @see \Benwilkins\Analyst\Analyst
 */
class AnalystFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-analyst';
    }
}