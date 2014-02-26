<?php

namespace IgraalOSL\StatsTable\Dumper;

interface FormatInterface
{
    const DATE     = 'date';
    const DATETIME = 'datetime';
    const FLOAT2   = 'float2';
    const INTEGER  = 'integer';
    const MONEY    = 'money';
    const MONEY2   = 'money2';
    const PCT      = 'percent';
    const PCT2     = 'percent2';
    const STRING   = 'string';

    /**
     * @param  string $type
     * @return boolean
     */
    public function supports($type);
}
