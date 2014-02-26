<?php

namespace IgraalOSL\StatsTable\Dumper\CSV;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\StatsTable;

class CSVDumper extends Dumper
{
    /** @var string The current locale */
    private $locale;
    private $delimiter;
    private $enclosure;

    public function __construct($delimiter = ',', $enclosure = '"', $locale = '')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->locale = $locale;
    }

    /**
     * The locale to use
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function dump(StatsTable $statsTable)
    {
        $fp = fopen('php://temp', 'w');

        if ($this->enableHeaders) {
            $this->writeLine($fp, $statsTable->getHeaders());
        }

        foreach ($statsTable->getData() as $line) {
            $this->writeLine($fp, $line, $statsTable->getDataFormats());
        }

        if ($this->enableAggregation) {
            $this->writeLine($fp, $statsTable->getAggregations(), $statsTable->getAggregationsFormats());
        }

        $len = ftell($fp);
        fseek($fp, 0, SEEK_SET);

        return fread($fp, $len);
    }

    private function writeLine($fp, $line, $formats = array())
    {
        foreach ($formats as $index => $format) {
            if (array_key_exists($index, $line)) {
                $line[$index] = $this->formatValue($format, $line[$index]);
            }
        }
        fputcsv($fp, $line, $this->delimiter, $this->enclosure);
    }
}
