<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\RatioAggregation;
use IgraalOSL\StatsTable\Dumper\Format;

class RatioAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $format = Format::FLOAT2;
        $subscribersRatio = new RatioAggregation('hits', 'subscribers', $format);
        $this->assertEquals(13/40, $subscribersRatio->aggregate($statsTable));
        $this->assertEquals($format, $subscribersRatio->getFormat());
    }
}
