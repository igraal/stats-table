<?php

namespace IgraalOSL\StatsTable\Dumper;

use IgraalOSL\StatsTable\StatsTable;

interface DumperInterface
{
    public function dump(StatsTable $statsTable);
}
