<?php

namespace Tests;

use IgraalOSL\StatsTable\StatsTable;

class StatsTableTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveColumn()
    {
        $statsTable = new StatsTable(
            array(
                array('a' => 'a', 'b' => 'b'),
                array('a' => 'A', 'b' => 'B')
            ),
            array('a' => 'Alpha', 'b' => 'Bravo')
        );

        $statsTable->removeColumn('b');

        $this->assertEquals(array('a' => 'Alpha'), $statsTable->getHeaders());

        $this->assertEquals(
            array(
                array('a' => 'a'),
                array('a' => 'A')
            ),
            $statsTable->getData()
        );
    }
}
