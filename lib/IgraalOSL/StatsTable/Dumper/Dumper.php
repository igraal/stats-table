<?php

namespace IgraalOSL\StatsTable\Dumper;

abstract class Dumper implements DumperInterface
{
    protected $enableHeaders = true;
    protected $enableAggregation = true;

    public function enableHeaders($enableHeaders = true)
    {
        $this->enableHeaders = $enableHeaders;
    }

    public function enableAggregation($enableAggregation = true)
    {
        $this->enableAggregation = $enableAggregation;
    }

    protected function formatValue($format, $value)
    {
        switch ($format)
        {
            case FormatInterface::DATE:
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d');
                }
                break;

            case FormatInterface::DATETIME:
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d H:i:s');
                }
                break;

            case FormatInterface::FLOAT2:
                return sprintf("%.2f", $value);

            case FormatInterface::INTEGER:
                return sprintf("%d", $value);

            case FormatInterface::PCT:
                return $this->formatValue(FormatInterface::INTEGER, $value)." %";

            case FormatInterface::PCT2:
                return $this->formatValue(FormatInterface::FLOAT2, $value)." %";

            case FormatInterface::MONEY:
                return $this->formatValue(FormatInterface::INTEGER, $value)." €";

            case FormatInterface::MONEY2:
                return $this->formatValue(FormatInterface::FLOAT2, $value)." €";
        }

        return $value;
    }
}
