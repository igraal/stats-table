<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\JSON\JSONDumper;

class JSONTest extends DumperTestAbstract
{
    public function testJSON()
    {
        $jsonDumper = new JSONDumper();

        // With all values
        $this->assertEquals(array(
            'headers' => $this->getHeaders(),
            'data' => $this->getData(),
            'aggregations' => array('hits' => 'value')
        ), json_decode($jsonDumper->dump($this->getStatsTable()), true));

        // Without aggregation
        $jsonDumper->enableAggregation(false);
        $this->assertEquals(array(
            'headers' => $this->getHeaders(),
            'data' => $this->getData(),
        ), json_decode($jsonDumper->dump($this->getStatsTable()), true));

        // Without aggregation nor headers
        $jsonDumper->enableHeaders(false);
        $this->assertEquals(array(
            'data' => $this->getData(),
        ), json_decode($jsonDumper->dump($this->getStatsTable()), true));
    }

    public function testFormats()
    {
    }
}
