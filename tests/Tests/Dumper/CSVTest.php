<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\CSV\CSVDumper;
use IgraalOSL\StatsTable\Dumper\FormatInterface;
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
            $csvDumper->dump(new StatsTable(array(array('date' => '2014-01-01'), array('date' => new \DateTime('2014-01-01'))), array(), array(), array('date' => FormatInterface::DATE)))
        );

        // DATETIME
        $this->assertEquals(
            "\"2014-01-01 00:00:00\"\n\"2014-01-01 00:00:00\"\n",
            $csvDumper->dump(new StatsTable(array(array('date' => '2014-01-01 00:00:00'), array('date' => new \DateTime('2014-01-01 00:00:00'))), array(), array(), array('date' => FormatInterface::DATETIME)))
        );

        // INTEGER
        $this->assertEquals(
            "132\n133\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.3)), array(), array(), array('test' => FormatInterface::INTEGER)))
        );

        // FLOAT2
        $this->assertEquals(
            "132.00\n133.35\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::FLOAT2)))
        );

        // MONEY
        $this->assertEquals(
            "\"132 €\"\n\"133 €\"\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::MONEY)))
        );

        // MONEY2
        $this->assertEquals(
            "\"132.00 €\"\n\"133.35 €\"\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::MONEY2)))
        );

        // PCT
        $this->assertEquals(
            "\"132 %\"\n\"133 %\"\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::PCT)))
        );

        // PCT2
        $this->assertEquals(
            "\"132.00 %\"\n\"133.35 %\"\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::PCT2)))
        );

        // String
        $this->assertEquals(
            "132\n133.351\n",
            $csvDumper->dump(new StatsTable(array(array('test' => 132), array('test' => 133.351)), array(), array(), array('test' => FormatInterface::STRING)))
        );

        $csvDumper->enableAggregation(true);
        $csvDumper->enableHeaders(true);
        $this->assertEquals(
            "Date,Hits\n2014-01-01,3\nTotal,3\n",
            $csvDumper->dump(new StatsTable(
                array(array('date' => '2014-01-01', 'hits' => 3)),
                array('date' => 'Date', 'hits' => 'Hits'),
                array('date' => 'Total', 'hits' => 3)
            ))
        );
    }
}
