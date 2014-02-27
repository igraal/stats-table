<?php

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

/**
 * Class StaticAggregation
 * Returns a static value. Useful for first column
 */
class StaticAggregation implements AggregationInterface
{
    private $value;
    private $format;

    public function __construct($value, $format = Format::STRING)
    {
        $this->value = $value;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
