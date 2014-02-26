<?php

namespace Tests;

use IgraalOSL\StatsTable\Aggregation\SumAggregation;
use IgraalOSL\StatsTable\StatsColumnBuilder;
use IgraalOSL\StatsTable\StatsTable;
use IgraalOSL\StatsTable\StatsTableBuilder;

class StatsTableBuilderTests extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $table = array(
            array('hits' => 12, 'subscribers' => 3),
            array('hits' => 25, 'subscribers' => 4)
        );

        $statsTable = new StatsTableBuilder(
            $table,
            array('hits' => 'Hits', 'subscribers' => 'Subscribers')
        );

        $this->assertEquals(new StatsColumnBuilder(array(array('hits' => 12), array('hits' => 25)), 'Hits'), $statsTable->getColumn('hits'));
    }

    public function testAdditionalIndexes()
    {
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );

        $defaultValues = array('hits' => 0);

        $wishedTable = $table;
        $wishedTable['2014-01-02'] = $defaultValues;

        $statsTable = new StatsTableBuilder(
            $table,
            array(),
            array(),
            array(),
            array(),
            $defaultValues,
            array_keys($wishedTable)
        );

        $this->assertEquals(
            new StatsColumnBuilder($wishedTable, 'hits'),
            $statsTable->getColumn('hits')
        );
    }

    public function testAddIndexAsColumn()
    {
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );

        $statsTable = new StatsTableBuilder($table);
        $statsTable->addIndexesAsColumn('date', 'Date');

        $dateColumn = new StatsColumnBuilder(array(
                '2014-01-01' => array('date' => '2014-01-01'),
                '2014-01-03' => array('date' => '2014-01-03')
            ), 'Date');

        $this->assertEquals($dateColumn, $statsTable->getColumn('date'));
    }

    public function testBuild()
    {
        // Test build with aggregation
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );

        $statsTable = new StatsTableBuilder(
            $table,
            array('hits' => 'Hits'),
            array(),
            array('hits' => new SumAggregation('hits'))
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $table,
            array('hits' => 'Hits'),
            array('hits' => 26)
        ), $stats);

        // Test build without aggregation
        $statsTable = new StatsTableBuilder(
            $table,
            array('hits' => 'Hits'),
            array(),
            array()
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $table,
            array('hits' => 'Hits'),
            array('hits' => null)
        ), $stats);
    }

    public function testMissingColumn()
    {
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array()
        );

        $defaultValues = array('hits' => 0);

        $statsTable = new StatsTableBuilder(
            $table,
            array('hits' => 'Hits'),
            array(),
            array(),
            array_keys($defaultValues),
            $defaultValues
        );

        $wishedTable = $table;
        $wishedTable['2014-01-03']['hits'] = 0;

        $this->assertEquals(new StatsColumnBuilder($wishedTable, 'Hits'), $statsTable->getColumn('hits'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidColumn()
    {
        $table = array(array('hits' => 0));
        $statsTable = new StatsTableBuilder($table);

        $statsTable->getColumn('invalidColumn');
    }

    public function testOrderColumns()
    {
        $table = array('a' => 'value1', 'b' => 'value2', 'c' => 'value3');
        $expectedTable = array('c' => 'value3', 'a' => 'value1');

        $this->assertEquals($expectedTable, StatsTableBuilder::orderColumns($table, array('c', 'a')));

        $this->assertEquals($table, StatsTableBuilder::orderColumns($table, array()));
    }
}
