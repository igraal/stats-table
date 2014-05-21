<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\CountAggregation;
use IgraalOSL\StatsTable\Aggregation\SumAggregation;

class CountAggregationTest extends AggregationTestAbstract
{
    public function testAggregation()
    {
        $statsTable = $this->getSampleTable();

        $lineNumber = new CountAggregation('hits');
        $this->assertEquals(2, $lineNumber->aggregate($statsTable));
    }
}
