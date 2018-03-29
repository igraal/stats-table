<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\Excel\ExcelDumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTable;

class ExcelTest extends DumperTestAbstract
{
    public function test()
    {
        $headers = ['date' => 'Date', 'hits' => 'Nb de visites', 'subscribers' => 'Nb inscrits', 'ratio' => 'Taux de transfo', 'revenues' => 'Revenus générés'];
        $data = [
            ['date' => '2014-01-01', 'hits' => '10', 'subscribers' => 2, 'ratio' => .2, 'revenues' => 45.321],
            ['date' => '2014-01-01', 'hits' => '20', 'subscribers' => 7, 'ratio' => .35, 'revenues' => 80.754],
        ];
        $dataTypes = [
            'date' => Format::DATE,
            'hits' => Format::INTEGER,
            'subscribers' => Format::INTEGER,
            'ratio' => Format::PCT2,
            'revenues' => Format::MONEY2,
        ];

        $aggregations = [
            'date' => 'Total',
            'hits' => '30',
            'subscribers' => '9',
            'ratio' => '.3',
            'revenues' => 126.075,
        ];

        $aggregationsTypes = $dataTypes;
        $aggregationsTypes['date'] = Format::STRING;
        $aggregationsTypes['ratio'] = Format::PCT;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes);
        $excelDumper = new ExcelDumper();
        $excelDumper->setOption(ExcelDumper::OPTION_ZEBRA, true);
        $excelDumper->setOption(ExcelDumper::OPTION_ZEBRA_COLOR_ODD, 'eeeeee');

        $excelContents = $excelDumper->dump($statsTable);
        file_put_contents('/tmp/test.xls', $excelContents);

        $dataTypes['date'] = Format::DATETIME;
        $dataTypes['revenues'] = Format::FLOAT2;
        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes);
        $excelDumper->setOptions([ExcelDumper::OPTION_ZEBRA => false]);
        $excelContents = $excelDumper->dump($statsTable);

        file_put_contents('/tmp/test2.xls', $excelContents);
    }
}
