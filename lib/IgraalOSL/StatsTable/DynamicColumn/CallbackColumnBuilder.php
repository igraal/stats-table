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
        $values = [];

        foreach ($statsTable->getIndexes() as $index) {
            // Recreate line
            $line = [];
            foreach ($statsTable->getColumns() as $columnName => $column) {
                $columnValues = $column->getValues();
                $line = array_merge($line, [$columnName => $columnValues[$index]]);
            }
            $values[$index] = call_user_func_array($this->callback, [$line]);
        }

        return $values;
    }
}
