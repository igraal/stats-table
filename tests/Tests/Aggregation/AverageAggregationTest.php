<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\AverageAggregation;
use IgraalOSL\StatsTable\Dumper\FormatInterface;

class AverageAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $format = FormatInterface::FLOAT2;
        $hitsAverage = new AverageAggregation('hits', $format);
        $this->assertEquals(20, $hitsAverage->aggregate($statsTable));
        $this->assertEquals($format, $hitsAverage->getFormat());
    }
}
