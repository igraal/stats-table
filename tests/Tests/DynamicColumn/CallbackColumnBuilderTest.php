<?php

namespace Tests\DynamicColumn;

use IgraalOSL\StatsTable\DynamicColumn\CallbackColumnBuilder;
use IgraalOSL\StatsTable\StatsTableBuilder;

class CallbackColumnBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $statsTableBuilder = new StatsTableBuilder([
            ['hits' => 10, 'subscribers' => 5],
            ['hits' => 5, 'subscribers' => 2]
        ]);

        $callbackColumnBuilder = new CallbackColumnBuilder(function($line) {
            return $line['hits'] * $line['subscribers'];
        });
        $this->assertEquals(
            [50, 10],
            $callbackColumnBuilder->buildColumnValues($statsTableBuilder)
        );
    }
}
