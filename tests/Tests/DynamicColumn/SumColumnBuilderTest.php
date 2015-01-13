<?php

namespace Tests\DynamicColumn;

use IgraalOSL\StatsTable\DynamicColumn\SumColumnBuilder;
use IgraalOSL\StatsTable\StatsColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

class SumColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $table = array(
            '2014-01-01' => array('hits' => 10, 'subscribers' => 5),
            '2014-01-02' => array('hits' => 30, 'subscribers' => 9),
            '2014-01-03' => array('hits' => 0, 'subscribers' => 0)
        );

        $statsTable = new StatsTableBuilder($table);

        $sumBuilder = new SumColumnBuilder(array('subscribers', 'hits'));

        $statsTable->addDynamicColumn('sum', $sumBuilder, 'Sum');

        $sumData = array(
            '2014-01-01' => 15,
            '2014-01-02' => 39,
            '2014-01-03' => 0
        );
        $sumColumn = new StatsColumnBuilder($sumData, 'Sum');
        $this->assertEquals($sumColumn, $statsTable->getColumn('sum'));
    }
}
