<?php

namespace Tests\DynamicColumn;

use IgraalOSL\StatsTable\DynamicColumn\RatioColumnBuilder;
use IgraalOSL\StatsTable\StatsColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

class RatioColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $table = [
            '2014-01-01' => ['hits' => 10, 'subscribers' => 5],
            '2014-01-02' => ['hits' => 30, 'subscribers' => 9],
            '2014-01-03' => ['hits' => 0, 'subscribers' => 0]
        ];

        $statsTable = new StatsTableBuilder($table);

        $ratioBuilder = new RatioColumnBuilder('subscribers', 'hits', 'N/A');

        $statsTable->addDynamicColumn('ratio', $ratioBuilder, 'Ratio');

        $ratioData = [
            '2014-01-01' => .5,
            '2014-01-02' => .3,
            '2014-01-03' => 'N/A'
        ];
        $ratioColumn = new StatsColumnBuilder($ratioData, 'Ratio');
        $this->assertEquals($ratioColumn, $statsTable->getColumn('ratio'));
    }
}
