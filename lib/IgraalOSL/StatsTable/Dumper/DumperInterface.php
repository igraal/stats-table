<?php

namespace IgraalOSL\StatsTable\Dumper;

use IgraalOSL\StatsTable\StatsTable;

interface DumperInterface
{
    /**
     * Dump the stats table
     * @param  StatsTable $statsTable The stats table to dump
     * @return string                 The stats table dumped
     */
    public function dump(StatsTable $statsTable);

    /**
     * Retrieve mime-type
     * @return string
     */
    public function getMimeType();
}
