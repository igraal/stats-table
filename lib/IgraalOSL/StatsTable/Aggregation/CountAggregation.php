<?php

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

/**
 * Class CountAggregation
 * Returns number of element in dataset
 */
class CountAggregation implements AggregationInterface
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

        return count($column);
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }
}
