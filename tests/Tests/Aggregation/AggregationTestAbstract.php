<?php

namespace Tests\Aggregation;

use IgraalOSL\StatsTable\StatsTableBuilder;

class AggregationTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * @return StatsTableBuilder
     */
    protected function getSampleTable()
    {
        $table = [
            '2014-01-01' => ['hits' => 10, 'subscribers' => 5],
            '2014-01-02' => ['hits' => 30, 'subscribers' => 8],
        ];

        return new StatsTableBuilder($table);
    }
}
