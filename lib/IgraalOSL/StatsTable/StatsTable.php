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
    public function __construct(
        array $data,
        array $headers = array(),
        array $aggregations = array(),
        array $dataFormats = array(),
        array $aggregationsFormats = array()
    ) {
        $this->headers = $headers;
        $this->data = $data;
        $this->aggregations = $aggregations;
        $this->dataFormats = $dataFormats;
        $this->aggregationsFormats = $aggregationsFormats;
    }

    /**
     * Remove a single column in table
     *
     * @param mixed $columnName
     *
     * @return StatsTable
     */
    public function removeColumn($columnName)
    {
        return $this->removeColumns(array($columnName));
    }

    /**
     * Remove columns in table
     *
     * @param array $columns
     *
     * @return StatsTable
     */
    public function removeColumns(array $columns)
    {
        $columnsMap = array_flip($columns);

        $this->removeColumnsInLine($this->headers, $columnsMap);
        $this->removeColumnsInLine($this->aggregations, $columnsMap);
        $this->removeColumnsInLine($this->dataFormats, $columnsMap);
        $this->removeColumnsInLine($this->aggregationsFormats, $columnsMap);

        foreach ($this->data as &$line) {
            $this->removeColumnsInLine($line, $columnsMap);
        }

        return $this;
    }

    /**
     * Internal helper to remove columns for a line.
     *
     * @param array $line       Line to filter. Referenced.
     * @param array $columnsMap An array indexed by columns to exclude. Value doesn't matter.
     *
     * @return void
     */
    protected function removeColumnsInLine(array &$line, array $columnsMap)
    {
        foreach ($line as $k => $v) {
            if (array_key_exists($k, $columnsMap)) {
                unset($line[$k]);
            }
        }
    }
}
