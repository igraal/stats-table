<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\AverageAggregation;
use IgraalOSL\StatsTable\Dumper\Format;

class AverageAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $format = Format::FLOAT2;
        $hitsAverage = new AverageAggregation('hits', $format);
        $this->assertEquals(20, $hitsAverage->aggregate($statsTable));
        $this->assertEquals($format, $hitsAverage->getFormat());
    }
}
