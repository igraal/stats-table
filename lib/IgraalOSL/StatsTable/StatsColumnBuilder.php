<?php

namespace IgraalOSL\StatsTable;

use IgraalOSL\StatsTable\Aggregation\AggregationInterface;

class StatsColumnBuilder
{
    /**
     * @param array                $values      Associative array like index => { name => value }
     * @param string               $headerName  Header name
     * @param string               $format      Format
     * @param AggregationInterface $aggregation Aggregation
     */
    public function __construct($values, $headerName = '', $format = null, AggregationInterface $aggregation = null, $metaData = [])
    {
        $this->values = $values;
        $this->headerName = $headerName;
        $this->format = $format;
        $this->aggregation = $aggregation;
        $this->metaData = $metaData;
    }

    /** @var Array The raw values */
    private $values;
    /** @var string The format */
    private $format;
    /** @var AggregationInterface The aggregation rule */
    private $aggregation;
    /** @var string The header name */
    private $headerName;
    /** @var array Column metadata */
    private $metaData;

    /**
     * @return mixed[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function getHeaderName()
    {
        return $this->headerName;
    }

    /**
     * @param  $headerName
     * @return $this
     */
    public function setHeaderName($headerName)
    {
        $this->headerName = $headerName;

        return $this;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregation()
    {
        return $this->aggregation;
    }

    /**
     * @param  AggregationInterface $aggregation
     * @return $this
     */
    public function setAggregation(AggregationInterface $aggregation)
    {
        $this->aggregation = $aggregation;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * @param array $metaData
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
    }



    /**
     * Ensure column is filled with given indexes. If not, it will be filled with default values
     * @param $indexes
     * @param $defaultValue
     */
    public function insureIsFilled($indexes, $defaultValue)
    {
        $newValues = [];
        foreach ($indexes as $index) {
            $newValues[$index] = array_key_exists($index, $this->values) ? $this->values[$index] : $defaultValue;
        }
        $this->values = $newValues;
    }
}
