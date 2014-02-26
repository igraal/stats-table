<?php

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\Dumper\FormatInterface;
use IgraalOSL\StatsTable\StatsTableBuilder;
use IgraalOSL\StatsTable\Tools\ArrayTools;

/**
 * Class SumAggregation
 * Returns the sum of all elements in the column
 */
class SumAggregation implements AggregationInterface
{
    private $columnName;
    private $format;

    public function __construct($columnName, $format = FormatInterface::INTEGER)
    {
        $this->columnName = $columnName;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        $column = $statsTable->getColumn($this->columnName)->getValues();
        return array_sum(ArrayTools::array_column($column, $this->columnName));
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }
}
