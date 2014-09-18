<?php

namespace Tests;

use IgraalOSL\StatsTable\Aggregation\StaticAggregation;
use IgraalOSL\StatsTable\Aggregation\SumAggregation;
use IgraalOSL\StatsTable\Dumper\Format;
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

        $this->assertEquals(new StatsColumnBuilder(array(12, 25), 'Hits'), $statsTable->getColumn('hits'));
    }

    public function testAdditionalIndexes()
    {
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );

        $defaultValues = array('hits' => 0);

        $wishedColumn = array(
            '2014-01-01' => 12,
            '2014-01-02' => 0,
            '2014-01-03' => 14,
        );

        $statsTable = new StatsTableBuilder(
            $table,
            array(),
            array(),
            array(),
            array(),
            $defaultValues,
            array_keys($wishedColumn)
        );

        $this->assertEquals(
            new StatsColumnBuilder($wishedColumn, 'hits'),
            $statsTable->getColumn('hits')
        );

        $this->assertEquals(array_keys($wishedColumn), array_keys($statsTable->getColumn('hits')->getValues()));
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
                '2014-01-01' => '2014-01-01',
                '2014-01-03' => '2014-01-03'
            ), 'Date');

        $this->assertEquals($dateColumn, $statsTable->getColumn('date'));
    }


    private function _getTestData()
    {
        // Data of test
        return array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );
    }

    public function testBuildWithAggregation()
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            array('hits' => 'Hits'),
            array('hits'=>Format::INTEGER),
            array('hits' => new SumAggregation('hits'))
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $data,
            array('hits' => 'Hits'),
            array('hits' => 26),
            array('hits'=>Format::INTEGER),
            array('hits'=>Format::INTEGER),
            array('hits'=>array())
        ), $stats);
    }

    public function testBuildWithoutAggregation()
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            array('hits' => 'Hits')
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $data,
            array('hits' => 'Hits'),
            array('hits' => null),
            array('hits'=>  null),
            array(),
            array('hits'=>  array())
        ), $stats);
    }

    public function testBuildWithoutData()
    {
        $statsTable = new StatsTableBuilder(
            array(),
            array('hits' => 'Hits')
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            array(),
            array('hits' => 'Hits'),
            array('hits' => null),
            array('hits'=>  null),
            array(),
            array('hits'=>  array())
        ), $stats);
    }

    public function testBuildWithoutDataAndWithAggregation()
    {
        $statsTable = new StatsTableBuilder(
            array(),
            array('hits' => 'Hits'),
            array('hits'=>Format::INTEGER),
            array('hits' => new SumAggregation('hits'))
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            array(),
            array('hits' => 'Hits'),
            array('hits' => 0),
            array('hits'=>Format::INTEGER),
            array('hits'=>Format::INTEGER),
            array('hits'=>array())
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

        $wishedColumn = array(
            '2014-01-01' => 12,
            '2014-01-03' => 0
        );

        $this->assertEquals(new StatsColumnBuilder($wishedColumn, 'Hits'), $statsTable->getColumn('hits'));
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

    public function testBuildWithOrder()
    {
        $table = array(
            array('a' => 'a', 'b' => 'b', 'c' => 'c'),
            array('a' => 'A', 'b' => 'B', 'c' => 'C'),
        );
        $headers = array(
            'a' => 'Alpha',
            'b' => 'Bravo',
            'c' => 'Charly'
        );

        $statsTableBuilder = new StatsTableBuilder(
            $table,
            $headers,
            array(Format::STRING, Format::STRING)
        );

        $statsTable = $statsTableBuilder->build(array('c', 'a'));
        $this->assertEquals(
            array('c' => 'Charly', 'a' => 'Alpha'),
            $statsTable->getHeaders()
        );
    }

    public function testGroupBy()
    {
        $table = array(
            array('tag' => 'one', 'subtag' => 'morning', 'hits' => 2),
            array('tag' => 'one', 'subtag' => 'afternoon', 'hits' => 3),
            array('tag' => 'two', 'subtag' => 'morning', 'hits' => 4),
        );
        $statsTableBuilder = new StatsTableBuilder(
            $table,
            array('tag' => 'Tag', 'subtag' => 'When', 'hits' => 'Hits'),
            array('tag' => Format::STRING, 'subtag' => Format::STRING, 'hits' => Format::INTEGER),
            array(
                'tag' => new StaticAggregation('Tag'),
                'subtag' => new StaticAggregation('Sub tag'),
                'hits' => new SumAggregation('hits', Format::INTEGER)
            )
        );

        $groupedByStatsTableBuilder = $statsTableBuilder->groupBy(array('tag'), array('subtag'));

        $this->assertEquals(2, count($groupedByStatsTableBuilder->getColumns()));

        $this->assertEquals(
            array('one', 'two'),
            $groupedByStatsTableBuilder->getColumn('tag')->getValues()
        );

        $this->assertEquals(
            array(5, 4),
            $groupedByStatsTableBuilder->getColumn('hits')->getValues()
        );

        $this->assertEquals(
            'Tag',
            $groupedByStatsTableBuilder->getColumn('tag')->getAggregation()->aggregate($groupedByStatsTableBuilder)
        );
        $this->assertEquals(
            9,
            $groupedByStatsTableBuilder->getColumn('hits')->getAggregation()->aggregate($groupedByStatsTableBuilder)
        );
    }
}
