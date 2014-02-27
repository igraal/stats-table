<?php

namespace IgraalOSL\StatsTable\Aggregation;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

class RatioAggregation implements AggregationInterface
{
    private $valueInternalName;
    private $overInternalName;
    private $format;

    public function __construct($overInternalName, $valueInternalName, $format = Format::PCT2)
    {
        $this->valueInternalName = $valueInternalName;
        $this->overInternalName = $overInternalName;
        $this->format = $format;
    }

    public function aggregate(StatsTableBuilder $statsTable)
    {
        // Use sum
        $sumValueAggregation = new SumAggregation($this->valueInternalName);
        $sumOverAggregation  = new SumAggregation($this->overInternalName);

        $sumValue = $sumValueAggregation->aggregate($statsTable);
        $sumOver  = $sumOverAggregation->aggregate($statsTable);

        return $sumOver ? $sumValue / $sumOver : $sumValue;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
