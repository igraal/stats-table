<?php

namespace IgraalOSL\StatsTable\DynamicColumn;

use IgraalOSL\StatsTable\StatsTableBuilder;

class SumColumnBuilder implements DynamicColumnBuilderInterface
{
    protected $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable)
    {
        $column = [];

        $columnsValues = array_map(
            function ($columnName) use ($statsTable) {
                return $statsTable->getColumn($columnName)->getValues();
            },
            $this->columns
        );

        foreach ($statsTable->getIndexes() as $index) {
            $lineValues = array_map(
                function ($array) use ($index) {
                    return $array[$index];
                },
                $columnsValues
            );

            $column[$index] = array_sum($lineValues);
        }

        return $column;
    }
}
