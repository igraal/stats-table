<?php

namespace IgraalOSL\StatsTable;

use IgraalOSL\StatsTable\Dumper\Format;

class StatsTable
{
    private $headers;
    private $aggregations;
    private $data;
    private $dataFormats;
    private $aggregationsFormats;
    private $metaData;

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
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * Constructs a new stats table
     * @param array $data
     * @param $headers
     * @param $aggregations
     * @param array $dataFormats
     * @param array $aggregationsFormats
     * @param array $metaData
     */
    public function __construct(
        array $data,
        array $headers = [],
        array $aggregations = [],
        array $dataFormats = [],
        array $aggregationsFormats = [],
        array $metaData = []
    ) {
        $this->headers = $headers;
        $this->data = $data;
        $this->aggregations = $aggregations;
        $this->dataFormats = $dataFormats;
        $this->aggregationsFormats = $aggregationsFormats;
        $this->metaData = $metaData;
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

    /**
     * Sort stats table by one column
     * @param string $columnName Name of column
     * @param bool $asc Sort direction : TRUE=>Ascending, FALSE=>Descending
     * @return StatsTable
     */
    public function sortColumn($columnName, $asc = true)
    {
        $this->sortMultipleColumn(array($columnName=>$asc));
        return $this;
    }

    /**
     * Sort stats table by one column with a custom compare function
     * @param string $columnName Name of column
     * @param function $compareFunc Custom compare function that should return 0, -1 or 1.
     * @return StatsTable
     */
    public function uSortColumn($columnName, $compareFunc)
    {
        $this->uSortMultipleColumn(array($columnName=>$compareFunc));
        return $this;
    }

    /**
     * Sort stats table by multiple column
     * @param array $columns Associative array : KEY=> column name (string), VALUE=> Sort direction (boolean)
     * @return $this
     */
    public function sortMultipleColumn($columns)
    {
        $compareFuncList = [];
        foreach($columns as $colName=>$asc) {
            $columnFormat = array_key_exists($colName, $this->dataFormats) ? $this->dataFormats[$colName] : Format::STRING;
            $compareFuncList[$colName] = $this->_getFunctionForFormat($columnFormat, $asc);
        }

        $this->uSortMultipleColumn($compareFuncList);
        return $this;
    }

    /**
     * Sort stats table by multiple column with a custom compare function
     * @param $columns $columns Associative array : KEY=> column name (string), VALUE=> Custom function (function)
     * @return $this
     */
    public function uSortMultipleColumn($columns)
    {
        $dataFormats = $this->dataFormats;
        $sort = function ($a, $b) use ($columns, $dataFormats) {
            foreach ($columns as $colName => $fn) {
                $tmp = $fn($a[$colName], $b[$colName]);
                if ($tmp !== 0) {
                    return $tmp;
                }
            }
            return 0;
        };

        uasort($this->data, $sort);
        return $this;
    }

    private function _getFunctionForFormat($format, $asc)
    {
        $genericFunc = function ($a, $b) use ($asc) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? ($asc ? -1 : 1) : ($asc ? 1 : -1);
        };

        $stringCmp = function ($a, $b) use ($asc) {
            $tmp = strcmp($a, $b);
            return $asc ? $tmp : -$tmp;
        };


        if (Format::STRING == $format) {
            return $stringCmp;
        } else {
            return $genericFunc;
        }
    }
}
