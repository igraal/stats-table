<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\Aggregation\StaticAggregation;

class StaticAggregationTest extends AggregationTestAbstract
{
    public function testStaticAggregation()
    {
        $statsTable = $this->getSampleTable();
        $staticAggregation = new StaticAggregation('value');
        $this->assertEquals('value', $staticAggregation->aggregate($statsTable));
    }
}
 