<?php

namespace IgraalOSL\StatsTable\Dumper\CSV;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\StatsTable;
use IgraalOSL\StatsTable\Tools\ParameterBag;

class CSVDumper extends Dumper
{
    /** @var string The current locale */
    private $locale;
    private $delimiter;
    private $enclosure;
    private $charset;

    public function __construct(array $options = [])
    {
        $bag = new ParameterBag($options);
        $this->delimiter = $bag->get('delimiter', ',');
        $this->enclosure = $bag->get('enclosure', '"');
        $this->locale    = $bag->get('locale', '');
        $this->charset   = $bag->get('charset', 'utf-8');
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

    private function writeLine($fileHandler, $line, $formats = [])
    {
        foreach ($formats as $index => $format) {
            if (array_key_exists($index, $line)) {
                $line[$index] = $this->formatValue($format, $line[$index]);
            }
        }
        fputcsv($fileHandler, $line, $this->delimiter, $this->enclosure);
    }

    public function getMimeType()
    {
        return sprintf('text/csv; charset=%s', $this->charset);
    }
}
