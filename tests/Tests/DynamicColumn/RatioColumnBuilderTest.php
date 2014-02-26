<?php

namespace Tests\DynamicColumn;

use IgraalOSL\StatsTable\DynamicColumn\RatioColumnBuilder;
use IgraalOSL\StatsTable\StatsColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

class RatioColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuilder()
    {
        $table = array(
            '2014-01-01' => array('hits' => 10, 'subscribers' => 5),
            '2014-01-02' => array('hits' => 30, 'subscribers' => 9),
            '2014-01-03' => array('hits' => 0, 'subscribers' => 0)
        );

        $statsTable = new StatsTableBuilder($table);

        $ratioBuilder = new RatioColumnBuilder('subscribers', 'hits', 'N/A');

        $statsTable->addDynamicColumn('ratio', $ratioBuilder, 'Ratio');

        $ratioData = array(
            '2014-01-01' => array('ratio' => .5),
            '2014-01-02' => array('ratio' => .3),
            '2014-01-03' => array('ratio' => 'N/A')
        );
        $ratioColumn = new StatsColumnBuilder($ratioData, 'Ratio');
        $this->assertEquals($ratioColumn, $statsTable->getColumn('ratio'));
    }
}
 