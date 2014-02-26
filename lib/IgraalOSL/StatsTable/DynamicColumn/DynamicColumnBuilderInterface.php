<?php

namespace IgraalOSL\StatsTable\DynamicColumn;

use IgraalOSL\StatsTable\StatsTableBuilder;

interface DynamicColumnBuilderInterface
{
    public function buildColumnValues(StatsTableBuilder $statsTable);
}
