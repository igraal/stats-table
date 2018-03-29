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
     * @var string
     */
    protected $templateFolder;

    /**
     * @var array
     */
    protected $templateOptions;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct($options = [])
    {
        $options = new ParameterBag($options);

        $this->template = $options->get('template', $this->getDefaultTemplate());
        $this->templateFolder = $options->get('templateFolder', $this->getDefaultTemplateFolder());
        $this->twig     = new \Twig_Environment(new \Twig_Loader_Filesystem($this->templateFolder));
        $this->templateOptions = $options->get('templateOptions',[]);
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
        $metaData = $statsTable->getMetaData();

        $data = $this->formatData($data, $format);
        $aggregations = $this->formatLine($aggregations, $aggregationsFormats);

        $params = [
            'headers'      => $statsTable->getHeaders(),
            'data'         => $data,
            'aggregations' => $aggregations,
            'metaData'     => $metaData
        ];

        $params = array_merge($params, $this->templateOptions);

        return $this->twig->render($this->template, $params);
    }

    protected function formatData($data, $format)
    {
        foreach ($data as &$line) {
            $line = $this->formatLine($line, $format);
        }

        return $data;
    }

    protected function formatLine($line, $format)
    {
        foreach ($line as $id => &$val) {
            if (array_key_exists($id, $format)) {
                $val = $this->formatValue($format[$id], $val);
            }
        }

        return $line;
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

    protected function getDefaultTemplateFolder()
    {
        return __DIR__.'/../../Resources/views';
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
