<?php

namespace IgraalOSL\StatsTable\DynamicColumn;

use IgraalOSL\StatsTable\StatsTableBuilder;

class RatioColumnBuilder implements DynamicColumnBuilderInterface
{
    private $valueInternalName;
    private $overInternalName;
    private $defaultValue;

    /**
     * @param string $valueInternalName The small value
     * @param string $overInternalName  The big value
     * @param mixed  $defaultValue      Default value if big value is null
     */
    public function __construct($valueInternalName, $overInternalName, $defaultValue)
    {
        $this->overInternalName = $overInternalName;
        $this->valueInternalName = $valueInternalName;
        $this->defaultValue = $defaultValue;
    }

    public function buildColumnValues(StatsTableBuilder $statsTable)
    {
        $column = [];
        $values = $statsTable->getColumn($this->valueInternalName)->getValues();
        $overs  = $statsTable->getColumn($this->overInternalName)->getValues();
        foreach ($statsTable->getIndexes() as $index) {
            $value = $values[$index];
            $over  = $overs[$index];
            $column[$index] = $over ? $value / $over : $this->defaultValue;
        }

        return $column;
    }
}
