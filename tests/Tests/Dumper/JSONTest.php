<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\Dumper\JSON\JSONDumper;
use IgraalOSL\StatsTable\StatsTable;

class JSONTest extends DumperTestAbstract
{
    public function testJSON()
    {
        $jsonDumper = new JSONDumper();

        // With all values
        $this->assertEquals(array(
            'headers' => $this->getHeaders(),
            'data' => $this->getData(),
            'aggregations' => array('hits' => 'value'),
            'aggregationsFormats' => array('hits'=>Format::STRING),
            'formats'=> $this->getFormats(),

        ), json_decode($jsonDumper->dump($this->getStatsTable()), true));
    }

    public function testJSONPct()
    {
        $jsonDumper = new JSONDumper();

        $data = array(array('pct' => .3123));
        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            array(),
            array('pct' => Format::PCT)
        );

        $this->assertEquals(array(
            'headers' => array('pct'),
            'data' => array(array('pct' => 31)),
            'aggregations' => array(),
            'aggregationsFormats' => array(),
            'formats' => array('pct' => Format::PCT)
        ), json_decode($jsonDumper->dump($statsTable), true));
    }

    public function testJSONPct2()
    {
        $jsonDumper = new JSONDumper();

        $data = array(array('pct' => .3123));
        $statsTable = new StatsTable(
            $data,
            array_keys(current($data)),
            array(),
            array('pct' => Format::PCT2)
        );

        $this->assertEquals(array(
            'headers' => array('pct'),
            'data' => array(array('pct' => 31.23)),
            'aggregations' => array(),
            'aggregationsFormats' => array(),
            'formats' => array('pct' => Format::PCT2)
        ), json_decode($jsonDumper->dump($statsTable), true));
    }
}
