<?php

namespace Tests\Dumper;

use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\Dumper\HTML\HTMLDumper;
use IgraalOSL\StatsTable\StatsTable;

class HTMLTest extends DumperTestAbstract
{
    public function testDump()
    {
        $headers = array('date' => 'Date', 'hits' => 'Nb de visites', 'subscribers' => 'Nb inscrits', 'ratio' => 'Taux de transfo', 'revenues' => 'Revenus générés');
        $data = array(
            array('date' => '2014-01-01', 'hits' => '10', 'subscribers' => 2, 'ratio' => .2, 'revenues' => 45.321),
            array('date' => '2014-01-01', 'hits' => '20', 'subscribers' => 7, 'ratio' => .35, 'revenues' => 80.754),
        );
        $dataTypes = array(
            'date' => Format::DATE,
            'hits' => Format::INTEGER,
            'subscribers' => Format::INTEGER,
            'ratio' => Format::PCT2,
            'revenues' => Format::MONEY2,
        );

        $aggregations = array(
            'date' => 'Total',
            'hits' => '30',
            'subscribers' => '9',
            'ratio' => '.3',
            'revenues' => 126.075,
        );

        $metaData = array(
            'date'=>array('description'=>'Date of the stats'),
            'hits'=>array('description'=>'Number of hits')
        );

        $aggregationsTypes = $dataTypes;
        $aggregationsTypes['date'] = Format::STRING;
        $aggregationsTypes['ratio'] = Format::PCT;

        $statsTable = new StatsTable($data, $headers, $aggregations, $dataTypes, $aggregationsTypes, $metaData);

        $dumper = new HTMLDumper();
        $html = $dumper->dump($statsTable);
        $doc = new \DOMDocument();
        $doc->loadXML($html);

//        $doc->save(__DIR__.'/Fixtures/test.html');

        $expectedDoc = new \DOMDocument();
        $expectedDoc->load(__DIR__.'/Fixtures/test.html');

        $this->assertEquals($expectedDoc, $doc);


        // Test with a custom template
        $dumper = new HTMLDumper(array('template'=>'custom.html.twig',
                                       'templateFolder'=>__DIR__.'/Fixtures/template',
                                       'templateOptions'=>array('title'=>'My title test')));
        $html = $dumper->dump($statsTable);

        $doc = new \DOMDocument();
        $doc->loadXML($html);

        $expectedDoc = new \DOMDocument();
        $expectedDoc->load(__DIR__.'/Fixtures/test-custom.html');

        $this->assertEquals($expectedDoc, $doc);
    }
}
