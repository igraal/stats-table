<?php

namespace IgraalOSL\StatsTable\Dumper\JSON;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\StatsTable;

class JSONDumper extends Dumper
{
    public function dump(StatsTable $statsTable)
    {
        $result = array(
            'data' => $statsTable->getData(),
        );


        if ($this->enableHeaders) {
            $result['headers'] = $statsTable->getHeaders();
        }

        if ($this->enableAggregation) {
            $result['aggregations'] = $statsTable->getAggregations();
        }

        return json_encode($result);
    }
}
