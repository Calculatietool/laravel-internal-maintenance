<?php

namespace CalculatieTool\IntMaint\Facades;

use Illuminate\Support\Facades\Facade;

class InternalMaintenance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'intmaint';
    }
}
