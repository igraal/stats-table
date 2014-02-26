<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\RatioAggregation;
use IgraalOSL\StatsTable\Dumper\FormatInterface;

class RatioAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $format = FormatInterface::FLOAT2;
        $subscribersRatio = new RatioAggregation('hits', 'subscribers', $format);
        $this->assertEquals(13/40, $subscribersRatio->aggregate($statsTable));
        $this->assertEquals($format, $subscribersRatio->getFormat());
    }
}
