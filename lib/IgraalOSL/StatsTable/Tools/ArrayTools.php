<?php

namespace IgraalOSL\StatsTable\Tools;

class ArrayTools
{
    /**
     * Do array_column for <PHP5.5
     * @param  array $array
     * @param  mixed $column_key
     * @param  mixed $index_key
     * @return array The column
     */
    public static function array_column(array $array, $column_key, $index_key = null)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column_key, $index_key);
        } else {
            $result = array();
            foreach ($array as $key => $line) {
                if (null !== $index_key) {
                    $key = $line[$index_key];
                }
                $value = $line[$column_key];
                $result[$key] = $value;
            }
            return $result;
        }
    }
}
