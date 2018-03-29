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
        $table = [
            ['hits' => 12, 'subscribers' => 3],
            ['hits' => 25, 'subscribers' => 4]
        ];

        $statsTable = new StatsTableBuilder(
            $table,
            ['hits' => 'Hits', 'subscribers' => 'Subscribers']
        );

        $this->assertEquals(new StatsColumnBuilder([12, 25], 'Hits'), $statsTable->getColumn('hits'));
    }

    public function testAdditionalIndexes()
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14]
        ];

        $defaultValues = ['hits' => 0];

        $wishedColumn = [
            '2014-01-01' => 12,
            '2014-01-02' => 0,
            '2014-01-03' => 14,
        ];

        $statsTable = new StatsTableBuilder(
            $table,
            [],
            [],
            [],
            [],
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
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14]
        ];

        $statsTable = new StatsTableBuilder($table);
        $statsTable->addIndexesAsColumn('date', 'Date');

        $dateColumn = new StatsColumnBuilder(
            [
                '2014-01-01' => '2014-01-01',
                '2014-01-03' => '2014-01-03'
            ],
            'Date'
        );

        $this->assertEquals($dateColumn, $statsTable->getColumn('date'));
    }


    private function _getTestData()
    {
        // Data of test
        return [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];
    }

    public function testBuildWithAggregation()
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            ['hits' => 'Hits'],
            ['hits'=>Format::INTEGER],
            ['hits' => new SumAggregation('hits')]
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $data,
            ['hits' => 'Hits'],
            ['hits' => 26],
            ['hits'=>Format::INTEGER],
            ['hits'=>Format::INTEGER],
            ['hits'=>[]]
        ), $stats);
    }

    public function testBuildWithoutAggregation()
    {
        $data = $this->_getTestData();
        $statsTable = new StatsTableBuilder(
            $data,
            ['hits' => 'Hits']
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            $data,
            ['hits' => 'Hits'],
            ['hits' => null],
            ['hits'=>  null],
            [],
            ['hits'=>  []]
        ), $stats);
    }

    public function testBuildWithoutData()
    {
        $statsTable = new StatsTableBuilder(
            [],
            ['hits' => 'Hits']
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            [],
            ['hits' => 'Hits'],
            ['hits' => null],
            ['hits'=>  null],
            [],
            ['hits'=>  []]
        ), $stats);
    }

    public function testBuildWithoutDataAndWithAggregation()
    {
        $statsTable = new StatsTableBuilder(
            [],
            ['hits' => 'Hits'],
            ['hits'=>Format::INTEGER],
            ['hits' => new SumAggregation('hits')]
        );

        $stats = $statsTable->build();
        $this->assertEquals(new StatsTable(
            [],
            ['hits' => 'Hits'],
            ['hits' => 0],
            ['hits'=>Format::INTEGER],
            ['hits'=>Format::INTEGER],
            ['hits'=>[]]
        ), $stats);
    }

    public function testMissingColumn()
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => []
        ];

        $defaultValues = ['hits' => 0];

        $statsTable = new StatsTableBuilder(
            $table,
            ['hits' => 'Hits'],
            [],
            [],
            array_keys($defaultValues),
            $defaultValues
        );

        $wishedColumn = [
            '2014-01-01' => 12,
            '2014-01-03' => 0
        ];

        $this->assertEquals(new StatsColumnBuilder($wishedColumn, 'Hits'), $statsTable->getColumn('hits'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidColumn()
    {
        $table = [['hits' => 0]];
        $statsTable = new StatsTableBuilder($table);

        $statsTable->getColumn('invalidColumn');
    }

    public function testOrderColumns()
    {
        $table = ['a' => 'value1', 'b' => 'value2', 'c' => 'value3'];
        $expectedTable = ['c' => 'value3', 'a' => 'value1'];

        $this->assertEquals($expectedTable, StatsTableBuilder::orderColumns($table, ['c', 'a']));

        $this->assertEquals($table, StatsTableBuilder::orderColumns($table, []));
    }

    public function testBuildWithOrder()
    {
        $table = [
            ['a' => 'a', 'b' => 'b', 'c' => 'c'],
            ['a' => 'A', 'b' => 'B', 'c' => 'C'],
        ];
        $headers = [
            'a' => 'Alpha',
            'b' => 'Bravo',
            'c' => 'Charly'
        ];

        $statsTableBuilder = new StatsTableBuilder(
            $table,
            $headers,
            [Format::STRING, Format::STRING]
        );

        $statsTable = $statsTableBuilder->build(['c', 'a']);
        $this->assertEquals(
            ['c' => 'Charly', 'a' => 'Alpha'],
            $statsTable->getHeaders()
        );
    }

    public function testGroupBy()
    {
        $table = [
            ['tag' => 'one', 'subtag' => 'morning', 'hits' => 2],
            ['tag' => 'one', 'subtag' => 'afternoon', 'hits' => 3],
            ['tag' => 'two', 'subtag' => 'morning', 'hits' => 4],
        ];
        $statsTableBuilder = new StatsTableBuilder(
            $table,
            ['tag' => 'Tag', 'subtag' => 'When', 'hits' => 'Hits'],
            ['tag' => Format::STRING, 'subtag' => Format::STRING, 'hits' => Format::INTEGER],
            [
                'tag' => new StaticAggregation('Tag'),
                'subtag' => new StaticAggregation('Sub tag'),
                'hits' => new SumAggregation('hits', Format::INTEGER)
            ]
        );

        $groupedByStatsTableBuilder = $statsTableBuilder->groupBy(['tag'], ['subtag']);

        $this->assertEquals(2, count($groupedByStatsTableBuilder->getColumns()));

        $this->assertEquals(
            ['one', 'two'],
            $groupedByStatsTableBuilder->getColumn('tag')->getValues()
        );

        $this->assertEquals(
            [5, 4],
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
