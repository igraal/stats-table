<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\CSV\CSVDumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTable;

class CSVTest extends DumperTestAbstract
{
    public function testFormats()
    {
        $csvDumper = new CSVDumper();
        $csvDumper->setLocale('');
        $csvDumper->enableAggregation(false);
        $csvDumper->enableHeaders(false);

        // DATE
        $this->assertEquals(
            "2014-01-01\n2014-01-01\n",
            $csvDumper->dump(new StatsTable([['date' => '2014-01-01'], ['date' => new \DateTime('2014-01-01')]], [], [], ['date' => Format::DATE]))
        );

        // DATETIME
        $this->assertEquals(
            "\"2014-01-01 00:00:00\"\n\"2014-01-01 00:00:00\"\n",
            $csvDumper->dump(new StatsTable([['date' => '2014-01-01 00:00:00'], ['date' => new \DateTime('2014-01-01 00:00:00')]], [], [], ['date' => Format::DATETIME]))
        );

        // INTEGER
        $this->assertEquals(
            "132\n133\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.3]], [], [], ['test' => Format::INTEGER]))
        );

        // FLOAT2
        $this->assertEquals(
            "132.00\n133.35\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::FLOAT2]))
        );

        // MONEY
        $this->assertEquals(
            "\"132 €\"\n\"133 €\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::MONEY]))
        );

        // MONEY2
        $this->assertEquals(
            "\"132.00 €\"\n\"133.35 €\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::MONEY2]))
        );

        // PCT
        $this->assertEquals(
            "\"132 %\"\n\"133 %\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::PCT]))
        );

        // PCT2
        $this->assertEquals(
            "\"132.00 %\"\n\"133.35 %\"\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::PCT2]))
        );

        // String
        $this->assertEquals(
            "132\n133.351\n",
            $csvDumper->dump(new StatsTable([['test' => 132], ['test' => 133.351]], [], [], ['test' => Format::STRING]))
        );

        $csvDumper->enableAggregation(true);
        $csvDumper->enableHeaders(true);
        $this->assertEquals(
            "Date,Hits\n2014-01-01,3\nTotal,3\n",
            $csvDumper->dump(new StatsTable(
                [['date' => '2014-01-01', 'hits' => 3]],
                ['date' => 'Date', 'hits' => 'Hits'],
                ['date' => 'Total', 'hits' => 3]
            ))
        );
    }
}
