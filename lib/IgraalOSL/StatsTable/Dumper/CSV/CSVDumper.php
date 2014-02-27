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
        $fileHandler = fopen('php://temp', 'w');

        if ($this->enableHeaders) {
            $this->writeLine($fileHandler, $statsTable->getHeaders());
        }

        foreach ($statsTable->getData() as $line) {
            $this->writeLine($fileHandler, $line, $statsTable->getDataFormats());
        }

        if ($this->enableAggregation) {
            $this->writeLine($fileHandler, $statsTable->getAggregations(), $statsTable->getAggregationsFormats());
        }

        $len = ftell($fileHandler);
        fseek($fileHandler, 0, SEEK_SET);

        return fread($fileHandler, $len);
    }

    private function writeLine($fileHandler, $line, $formats = array())
    {
        foreach ($formats as $index => $format) {
            if (array_key_exists($index, $line)) {
                $line[$index] = $this->formatValue($format, $line[$index]);
            }
        }
        fputcsv($fileHandler, $line, $this->delimiter, $this->enclosure);
    }
}
