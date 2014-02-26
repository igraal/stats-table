<?php

namespace IgraalOSL\StatsTable;

class StatsTable
{
    private $headers;
    private $aggregations;
    private $data;
    private $dataFormats;
    private $aggregationsFormats;

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAggregationsFormats()
    {
        return $this->aggregationsFormats;
    }

    /**
     * @return array
     */
    public function getDataFormats()
    {
        return $this->dataFormats;
    }

    /**
     * Constructs a new stats table
     * @param array $data
     * @param $headers
     * @param $aggregations
     * @param array $dataFormats
     * @param array $aggregationsFormats
     */
    public function __construct(array $data, array $headers = array(), array $aggregations = array(), array $dataFormats = array(), array $aggregationsFormats = array())
    {
        $this->headers = $headers;
        $this->data = $data;
        $this->aggregations = $aggregations;
        $this->dataFormats = $dataFormats;
        $this->aggregationsFormats = $aggregationsFormats;
    }
}
