<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Aggregation\StaticAggregation;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTableBuilder;

class DumperTestAbstract extends \PHPUnit_Framework_TestCase
{
    public function getData()
    {
        $table = [
            '2014-01-01' => ['hits' => 12],
            '2014-01-03' => ['hits' => 14],
        ];

        return $table;
    }

    public function getHeaders()
    {
        return ['hits' => 'Hits'];
    }

    public function getFormats()
    {
        return ['hits' => Format::INTEGER];
    }

    public function getAggregations()
    {
        return ['hits' => new StaticAggregation('value')];
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
