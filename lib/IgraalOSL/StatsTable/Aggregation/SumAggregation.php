<?php

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

/**
 * Class SumAggregation
 * Returns the sum of all elements in the column
 */
class SumAggregation implements AggregationInterface
{
    private $columnName;
    private $format;

    public function __construct($columnName, $format = Format::INTEGER)
    {
        $this->columnName = $columnName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();

        return array_sum($column);
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }
}
