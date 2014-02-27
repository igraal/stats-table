<?php

namespace IgraalOSL\StatsTable\DynamicColumn;

use IgraalOSL\StatsTable\StatsTableBuilder;

class CallbackColumnBuilder implements DynamicColumnBuilderInterface
{
    /** @var callable */
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable)
    {
        $values = array();

        foreach ($statsTable->getIndexes() as $index) {
            // Recreate line
            $line = array();
            foreach ($statsTable->getColumns() as $columnName => $column) {
                $columnValues = $column->getValues();
                $line = array_merge($line, array($columnName => $columnValues[$index]));
            }
            $values[$index] = call_user_func_array($this->callback, array($line));
        }

        return $values;
    }
}
