<?php

namespace Tests\DynamicColumn;

use IgraalOSL\StatsTable\DynamicColumn\RelativeColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

class RelativeColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testWithData()
    {
        $statsTable = new StatsTableBuilder(
            array(
                'first' => array('a' => 1, 'b' => 2, 'c' => 0),
                'second' => array('a' => 4, 'b' => 5, 'd' => 0)
            )
        );

        $aColumnBuilder = new RelativeColumnBuilder('a');
        $this->assertEquals(
            array('first' => .2, 'second' => .8),
            $aColumnBuilder->buildColumnValues($statsTable)
        );

        $abColumnBuilder = new RelativeColumnBuilder(array('a', 'b'));
        $this->assertEquals(
            array('first' => .25, 'second' => .75),
            $abColumnBuilder->buildColumnValues($statsTable)
        );

        $cColumnBuilder = new RelativeColumnBuilder('c');
        $this->assertEquals(
            array('first' => 0, 'second' => 0),
            $cColumnBuilder->buildColumnValues($statsTable)
        );
    }
}
