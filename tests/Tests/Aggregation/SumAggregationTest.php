<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\SumAggregation;

class SumAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $hitsSum = new SumAggregation('hits');
        $this->assertEquals(40, $hitsSum->aggregate($statsTable));
    }
}
