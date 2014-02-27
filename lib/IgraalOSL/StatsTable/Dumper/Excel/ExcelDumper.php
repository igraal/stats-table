<?php

namespace IgraalOSL\StatsTable\Dumper\Excel;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTable;

class ExcelDumper extends Dumper
{
    const OPTION_ZEBRA = 'zebra';
    const OPTION_ZEBRA_COLOR_ODD = 'zebra_color_odd';
    const OPTION_ZEBRA_COLOR_EVEN = 'zebra_color_even';
    const OPTION_HEADER_FORMAT = 'header_format';

    const FORMAT_EUR = '# ##0.00 €';
    const FORMAT_DATETIME = 'dd/mm/yy hh:mm';

    protected $options = array();

    /**
     * Constructor
     * @param array $options An array with options
     */
    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * Set options defined in array. Does not replace existing ones
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Set specific option
     * @param string $optionName
     * @param mixed  $optionValue
     */
    public function setOption($optionName, $optionValue)
    {
        $this->options[$optionName] = $optionValue;
    }

    /**
     * Dumps the stats table
     * @param  StatsTable $statsTable
     * @return string
     */
    public function dump(StatsTable $statsTable)
    {
        $excel = new \PHPExcel();

        $excel->getDefaultStyle()->applyFromArray($this->getDefaultStyleArray());

        $sheet = $excel->getSheet();

        $row = 1;
        $data = $statsTable->getData();
        $width = count(reset($data));

        // HEADERS //
        if ($this->enableHeaders) {
            $headerStyle = new \PHPExcel_Style();
            $headerStyle->applyFromArray($this->getHeadersStyleArray());

            $col = 0;
            foreach ($statsTable->getHeaders() as $header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $header);
                $col++;
            }
            $sheet->duplicateStyle($headerStyle, 'A1:'.\PHPExcel_Cell::stringFromColumnIndex($width-1).'1');
            $row++;
        }

        // DATA //
        foreach ($statsTable->getData() as $data) {
            $this->applyValues($sheet, $row, $data, $statsTable->getDataFormats());
            $row++;
        }

        // AGGREGATIONS //
        if ($this->enableAggregation) {
            $this->applyValues($sheet, $row, $statsTable->getAggregations(), $statsTable->getAggregationsFormats(), $this->getAggregationsStyleArray());
        }

        // FINAL FORMATTING //
        for ($col = 0; $col < $width; $col++) {
            $sheet
                ->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($col))
                ->setAutoSize(true);
        }

        $fileHandler = fopen('php://temp', 'w');
        $xlsDumper = new \PHPExcel_Writer_Excel5($excel);
        $xlsDumper->save($fileHandler);

        unset($excel);
        unset($xlsDumper);

        $len = ftell($fileHandler);
        fseek($fileHandler, 0, SEEK_SET);

        $contents = fread($fileHandler, $len);

        return $contents;
    }

    /**
     * Get default style
     * @return array
     */
    protected function getDefaultStyleArray()
    {
        return array(
            'font' => array('name' => 'Arial', 'size' => 9),
        );
    }

    /**
     * Get default style for a filled cell
     * @return array
     */
    protected function getDefaultStyleForFilledCells()
    {
        return array_merge_recursive(
            $this->getDefaultStyleArray(),
            array(
                'borders' => array(
                    'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
                )
            )
        );
    }

    /**
     * Get default style for a given row
     * @param  integer $row
     * @return array
     */
    protected function getDefaultStyleArrayForRow($row)
    {
        $style = $this->getDefaultStyleForFilledCells();

        if ($this->getOption(self::OPTION_ZEBRA)) {
            if (($row % 2) == 0) {
                $bgColor = $this->getOption(self::OPTION_ZEBRA_COLOR_EVEN);
            } else {
                $bgColor = $this->getOption(self::OPTION_ZEBRA_COLOR_ODD);
            }

            if ($bgColor) {
                $style['fill'] = array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => $bgColor)
                );
            }
        }

        return $style;
    }

    /**
     * Get style for headers
     * @return array
     */
    protected function getHeadersStyleArray()
    {
        return array_merge_recursive(
            $this->getDefaultStyleForFilledCells(),
            array(
                'borders' => array(
                    'bottom'     => array(
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                        'color' => array(
                            'rgb' => '000000'
                        )
                    )
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'd0d0d0')
                ),
                'font' => array('bold' => true)
            )
        );
    }

    /**
     * Get style for aggregations
     * @return array
     */
    protected function getAggregationsStyleArray()
    {
        return array_merge_recursive(
            $this->getDefaultStyleForFilledCells(),
            array(
                'borders' => array(
                    'top'     => array(
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                        'color' => array(
                            'rgb' => '000000'
                        )
                    )
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'd0d0d0')
                ),
                'font' => array('bold' => true)
            )
        );
    }

    /**
     * Gets an option
     * @param  $optionName
     * @return null
     */
    public function getOption($optionName)
    {
        if (array_key_exists($optionName, $this->options)) {
            return $this->options[$optionName];
        } else {
            return null;
        }
    }

    /**
     * Set values in specific row
     * @param \PHPExcel_Worksheet $sheet      The worksheet
     * @param integer             $row        The selected row
     * @param array               $values     The values to insert
     * @param array               $formats    Associative arrays with formats
     * @param array               $styleArray An array representing the style
     */
    protected function applyValues(\PHPExcel_Worksheet $sheet, $row, $values, $formats, $styleArray = array())
    {
        $col = 0;
        foreach ($values as $index => $value) {
            $this->applyValue($sheet, $col, $row, $value, array_key_exists($index, $formats) ? $formats[$index] : Format::STRING, $styleArray);
            $col++;
        }
    }

    /**
     * Set value in specific cell
     * @param \PHPExcel_Worksheet $sheet      The worksheet
     * @param integer             $col        The selected column
     * @param integer             $row        The selected row
     * @param array               $value      The values to insert
     * @param array               $format     Associative arrays with formats
     * @param array               $styleArray An array representing the style
     * @param $row
     */
    protected function applyValue(\PHPExcel_Worksheet $sheet, $col, $row, $value, $format, $styleArray = array())
    {
        if (0 == count($styleArray)) {
            $styleArray = $this->getDefaultStyleArrayForRow($row);
        }

        $style = new \PHPExcel_Style();
        $style->applyFromArray($styleArray);

        switch ($format) {
            case Format::DATE:
                $date = new \DateTime($value);
                $value = \PHPExcel_Shared_Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                break;

            case Format::DATETIME:
                $date = new \DateTime($value);
                $value = \PHPExcel_Shared_Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(self::FORMAT_DATETIME);
                break;

            case Format::FLOAT2:
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                break;

            case Format::INTEGER:
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
                break;

            case Format::MONEY:
            case Format::MONEY2:
                $style->getNumberFormat()->setFormatCode(self::FORMAT_EUR);
                break;

            case Format::PCT:
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                break;

            case Format::PCT2:
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                break;

            case Format::STRING:
                $style->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                break;
        }

        $sheet->setCellValueByColumnAndRow($col, $row, $value);
        $sheet->duplicateStyle($style, \PHPExcel_Cell::stringFromColumnIndex($col).$row);
    }
}
