<?php

namespace IgraalOSL\StatsTable;

use IgraalOSL\StatsTable\Aggregation\AggregationInterface;
use IgraalOSL\StatsTable\Aggregation\StaticAggregation;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\DynamicColumn\DynamicColumnBuilderInterface;

class StatsTableBuilder
{
    /** @var StatsColumnBuilder[] The raw values passed to setTable, associative array */
    private $columns;

    /** @var mixed[] The lines indexes */
    private $indexes;

    /**
     * @param $table
     * @param array $headers
     * @param array $formats
     * @param array $aggregations
     * @param array $columnNames
     * @param array $defaultValues
     * @param null $indexes
     * @throws \InvalidArgumentException
     */
    public function __construct($table, $headers = array(), $formats = array(), $aggregations = array(), $columnNames = array(), array $defaultValues = array(), $indexes = null)
    {
        $this->columns = array();

        if (null !== $indexes) {
            $this->indexes = $indexes;
        } else {
            $this->indexes = array_keys($table);
        }

        $this->defaultValues = $defaultValues;

        // Append the values
        $this->appendTable($table, $headers, $formats, $aggregations, $columnNames);
    }

    /**
     * Retrieve existing indexes
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Add index of data as a new column
     *
     * @param $columnName
     * @param null $headerName
     * @param null $format
     * @param AggregationInterface $aggregation
     */
    public function addIndexesAsColumn($columnName, $headerName = null, $format = null, AggregationInterface $aggregation = null)
    {
        $values = array();
        foreach ($this->indexes as $index) {
            $values[$index] = $index;
        }

        $column = new StatsColumnBuilder(
            $values,
            $headerName,
            $format,
            $aggregation
        );

        $columns = array_reverse($this->columns);
        $columns[$columnName] = $column;
        $this->columns = $columns;
    }

    /**
     * Append columns given a table
     *
     * @param array                  $table
     * @param string[]               $headers
     * @param string[]               $formats
     * @param AggregationInterface[] $aggregations
     * @param string[]               $columnNames
     * @param mixed[]                $defaultValues
     */
    public function appendTable(
        $table,
        $headers,
        $formats,
        $aggregations,
        $columnNames = array(),
        $defaultValues = array()
    ) {
        $this->defaultValues = array_merge($this->defaultValues, $defaultValues);

        if (count($columnNames) === 0 && count($table) !== 0) {
            $columnNames = array_keys(reset($table));
        }

        if (count($columnNames) === 0 && count($headers) !== 0) {
            $columnNames = array_keys($headers);
        }

        foreach ($columnNames as $columnName) {
            $column = new StatsColumnBuilder(
                $this->getAssocColumn($table, $columnName),
                $this->getParameter($headers, $columnName, $columnName),
                $this->getParameter($formats, $columnName),
                $this->getParameter($aggregations, $columnName)
            );

            if (count($this->defaultValues)) {
                $column->insureIsFilled($this->indexes, $this->defaultValues[$columnName]);
            }

            $this->columns[$columnName] = $column;
        }
    }

    /**
     * Get an indexed value in a table. Same as ParameterBag
     * @param  array $values
     * @param  mixed $key
     * @param  mixed $defaultValue
     * @return mixed
     */
    private function getParameter($values, $key, $defaultValue = null)
    {
        return array_key_exists($key, $values) ? $values[$key] : $defaultValue;
    }

    /**
     * Returns an associative table only with selected column.
     * Fill with default value if column not in a row
     * @param  array  $table
     * @param  string $columnName
     * @param  mixed  $defaultValue
     * @return array  The column
     */
    public function getAssocColumn($table, $columnName, $defaultValue = null)
    {
        $values = array();
        foreach ($table as $key => $line) {
            if (array_key_exists($columnName, $line)) {
                $values[$key] = $line[$columnName];
            } else {
                $values[$key] = $defaultValue;
            }
        }

        return $values;
    }

    /**
     * Retrieve a column
     * @return StatsColumnBuilder
     * @throws \InvalidArgumentException
     */
    public function getColumn($columnName)
    {
        if (!array_key_exists($columnName, $this->columns)) {
            throw new \InvalidArgumentException('Unable to find column '.$columnName.' in columns '.join(',', array_keys($this->columns)));
        }

        return $this->columns[$columnName];
    }

    /**
     * Add a dynamic column
     * @param mixed                         $columnName
     * @param DynamicColumnBuilderInterface $dynamicColumn
     * @param string                        $header
     * @param string                        $format
     * @param AggregationInterface          $aggregation
     */
    public function addDynamicColumn($columnName, DynamicColumnBuilderInterface $dynamicColumn, $header = '', $format = null, AggregationInterface $aggregation = null)
    {
        $values = $dynamicColumn->buildColumnValues($this);
        $this->columns[$columnName] = new StatsColumnBuilder($values, $header, $format, $aggregation);
    }

    /**
     * Build the data
     * @param  array      $columns Desired columns
     * @return StatsTable
     */
    public function build($columns = array())
    {
        $headers = array();
        $data = array();
        $dataFormats = array();
        $aggregations = array();
        $aggregationsFormats = array();

        foreach ($this->indexes as $index) {
            $columnsNames = array_keys($this->columns);

            $line = array();
            foreach ($columnsNames as $columnName) {
                $columnValues = $this->columns[$columnName]->getValues();
                $line = array_merge($line, array($columnName => $columnValues[$index]));
            }

            $data[$index] = $this->orderColumns($line, $columns);
        }

        foreach ($this->columns as $columnName => $column) {
            $dataFormats[$columnName] = $column->getFormat();

            $headers = array_merge($headers, array($columnName => $column->getHeaderName()));

            $columnAggregation = $column->getAggregation();
            if ($columnAggregation) {
                $aggregationValue = $columnAggregation->aggregate($this);
                $aggregationsFormats[$columnName] = $columnAggregation->getFormat();
            } else {
                $aggregationValue = null;
            }
            $aggregations = array_merge($aggregations, array($columnName => $aggregationValue));
        }

        $headers = $this->orderColumns($headers, $columns);
        $aggregations = $this->orderColumns($aggregations, $columns);

        return new StatsTable($data, $headers, $aggregations, $dataFormats, $aggregationsFormats);
    }

    /**
     * Order table columns given columns table
     * @param  array $table
     * @param  array $columns
     * @return array
     */
    public static function orderColumns($table, $columns)
    {
        // If no columns given, return table as-is
        if (!$columns) {
            return $table;
        }

        // Order
        $result = array();
        foreach ($columns as $column) {
            if (array_key_exists($column, $table)) {
                $result[$column] = $table[$column];
            }
        }

        return $result;
    }

    /**
     * @return StatsColumnBuilder[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Do a groupBy on columns, using aggregations to aggregate data per line
     *
     * @param string|array $columns        Columns to aggregate
     * @param array        $excludeColumns Irrelevant columns to exclude
     *
     * @return StatsTableBuilder
     */
    public function groupBy($columns, array $excludeColumns = array())
    {
        $groupedData = array();
        $statsTable = $this->build();

        foreach ($statsTable->getData() as $line) {
            $key = join(
                '-_##_-',
                array_map(
                    function ($c) use ($line) {
                        return $line[$c];
                    },
                    $columns
                )
            );

            $groupedData[$key][] = $line;
        }

        $filterLine = function ($line) use ($excludeColumns) {
            foreach ($excludeColumns as $c) {
                unset($line[$c]);
            }

            return $line;
        };

        $headers = $filterLine(
            array_map(
                function (StatsColumnBuilder $c) {
                    return $c->getHeaderName();
                },
                $this->columns
            )
        );
        $formats = $filterLine(
            array_map(
                function (StatsColumnBuilder $c) {
                    return $c->getFormat();
                },
                $this->columns
            )
        );
        $aggregations = $filterLine(
            array_map(
                function (StatsColumnBuilder $c) {
                    return $c->getAggregation();
                },
                $this->columns
            )
        );

        $data = array();

        foreach ($groupedData as $lines) {
            $tmpAggregations = $aggregations;
            // Add static aggragation for group by fields
            foreach ($columns as $column) {
                $oneLine = current($lines);
                $value = $oneLine[$column];
                $tmpAggregations[$column] = new StaticAggregation($value, Format::STRING);
            }

            $tmpTableBuilder = new StatsTableBuilder(
                array_map($filterLine, $lines),
                $headers,
                $formats,
                $tmpAggregations
            );
            $tmpTable = $tmpTableBuilder->build();
            $data[] = $tmpTable->getAggregations();
        }

        return new StatsTableBuilder(
            $data,
            $headers,
            $formats,
            $aggregations
        );
    }
}
