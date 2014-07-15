<?php

namespace IgraalOSL\StatsTable\Dumper\HTML;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTable;
use IgraalOSL\StatsTable\Tools\ParameterBag;

class HTMLDumper extends Dumper
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct($options = array())
    {
        $options = new ParameterBag($options);

        $this->template = $options->get('template', $this->getDefaultTemplate());
        $this->twig     = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/../../Resources/views'));
    }

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function dump(StatsTable $statsTable)
    {
        $data = $statsTable->getData();
        $format = $statsTable->getDataFormats();
        $aggregations = $statsTable->getAggregations();
        $aggregationsFormats = $statsTable->getAggregationsFormats();

        $formatLine = function($line, $format)
        {
            foreach($line as $id=>&$val)
            {
                if(array_key_exists($id, $format)) {
                    $val = $this->formatValue($format[$id], $val);
                }
            }

            return $line;
        };

        $formatCollection = function($collection, $format) use ($formatLine)
        {
            foreach($collection as &$line)
            {
                $line = $formatLine($line, $format);
            }
            return $collection;
        };

        $data = $formatCollection($data, $format);
        $aggregations = $formatLine($aggregations, $aggregationsFormats);

        return $this->twig->render($this->template,
                                   array('headers'      => $statsTable->getHeaders(),
                                         'data'         => $data,
                                         'aggregations' => $aggregations));
    }

    /**
     * Format values for HTML View
     * @param $format
     * @param $value
     * @return float|int|string
     */
    protected function formatValue($format, $value)
    {
        // TODO : Put in parameters
        $decimals = 2;
        $dec_point = ',';
        $thousands_sep = ' ';

        switch ($format) {
            case Format::DATE:
                if ($value instanceof \DateTime) {
                    return $value->format('d/m/Y');
                }
                break;

            case Format::DATETIME:
                if ($value instanceof \DateTime) {
                    return $value->format('d/m/Y H:i:s');
                }
                break;

            case Format::FLOAT2:
                return str_replace($dec_point."00", "",number_format(floatval($value), $decimals, $dec_point, $thousands_sep));

            case Format::INTEGER:
                return number_format(intval($value), 0, $dec_point, $thousands_sep);

            case Format::PCT:
                return $this->formatValue(Format::INTEGER, $value*100)."%";

            case Format::PCT2:
                return $this->formatValue(Format::FLOAT2, $value*100)."%";

            case Format::MONEY:
                return $this->formatValue(Format::INTEGER, $value)."€";

            case Format::MONEY2:
                return $this->formatValue(Format::FLOAT2, $value)."€";
        }

        return $value;
    }

    protected function getDefaultTemplate()
    {
        return 'statsTable.html.twig';
    }

    public function getMimeType()
    {
        return 'text/html; charset=utf-8';
    }
}
