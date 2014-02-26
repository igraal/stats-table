<?php

namespace Tests;

use IgraalOSL\StatsTable\StatsColumnBuilder;

class StatsColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $aggregationMock = $this->getMock('IgraalOSL\StatsTable\Aggregation\AggregationInterface');

        $values = array(array('hits' => 3), array('hits' => 5));
        $column = new StatsColumnBuilder($values, 'Hits', 'format', $aggregationMock);

        $this->assertEquals($values, $column->getValues());
        $this->assertEquals('Hits', $column->getHeaderName());
        $this->assertEquals('format', $column->getFormat());
        $this->assertEquals($aggregationMock, $column->getAggregation());
    }

    public function testEnsureIndexExists()
    {
        $values = array('2014-01-01' => array('hits' => 3), '2014-01-03' => array('hits' => 5));
        $column = new StatsColumnBuilder($values);

        $column->insureIsFilled(array('2014-01-01', '2014-01-02', '2014-01-03'), array('hits' => 0));

        $values['2014-01-02'] = array('hits' => 0);

        $this->assertEquals($values, $column->getValues());
    }

    public function testSetters()
    {
        $aggregationMock = $this->getMock('IgraalOSL\StatsTable\Aggregation\AggregationInterface');

        $values = array(array('hits' => 3), array('hits' => 5));
        $column = new StatsColumnBuilder($values, 'Hits');

        $column->setHeaderName('Hits2');
        $this->assertEquals('Hits2', $column->getHeaderName());

        $this->assertNull($column->getAggregation());

        $column->setAggregation($aggregationMock);
        $this->assertEquals($aggregationMock, $column->getAggregation());
    }
}
