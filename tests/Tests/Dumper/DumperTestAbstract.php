<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Aggregation\StaticAggregation;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

class DumperTestAbstract extends \PHPUnit_Framework_TestCase
{
    public function getData()
    {
        $table = array(
            '2014-01-01' => array('hits' => 12),
            '2014-01-03' => array('hits' => 14)
        );

        return $table;
    }

    public function getHeaders()
    {
        return array('hits' => 'Hits');
    }

    public function getFormats()
    {
        return array('hits' => Format::INTEGER);
    }

    public function getAggregations()
    {
        return array('hits' => new StaticAggregation('value'));
    }

    public function getStatsTableBuilder()
    {
        return new StatsTableBuilder($this->getData(), $this->getHeaders(), $this->getFormats(), $this->getAggregations());
    }

    public function getStatsTable()
    {
        return $this->getStatsTableBuilder()->build();
    }
}
 