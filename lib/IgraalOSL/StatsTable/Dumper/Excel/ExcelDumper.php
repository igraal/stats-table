<?php

namespace IgraalOSL\StatsTable\Dumper\Excel;

use IgraalOSL\StatsTable\Dumper\Dumper;
use IgraalOSL\StatsTable\Dumper\Format;
use IgraalOSL\StatsTable\StatsTable;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelDumper extends Dumper
{
    const OPTION_ZEBRA = 'zebra';
    const OPTION_ZEBRA_COLOR_ODD = 'zebra_color_odd';
    const OPTION_ZEBRA_COLOR_EVEN = 'zebra_color_even';
    const OPTION_HEADER_FORMAT = 'header_format';

    const FORMAT_EUR = '# ##0.00 €';
    const FORMAT_DATETIME = 'dd/mm/yy hh:mm';

    protected $options = [];

    /**
     * Constructor
     * @param array $options An array with options
     */
    public function __construct($options = [])
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
     * @throws \Exception
     */
    public function dump(StatsTable $statsTable)
    {
        $excel = new Spreadsheet();

        $excel->getDefaultStyle()->applyFromArray($this->getDefaultStyleArray());

        $sheet = $excel->getActiveSheet();

        $row = 1;
        $data = $statsTable->getData();
        $width = count(reset($data));

        // HEADERS //
        if ($this->enableHeaders) {
            $headerStyle = new Style();
            $headerStyle->applyFromArray($this->getHeadersStyleArray());

            $col = 0;
            foreach ($statsTable->getHeaders() as $header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $header);
                $col++;
            }
            $sheet->duplicateStyle($headerStyle, 'A1:'. Coordinate::stringFromColumnIndex($width-1).'1');
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
                ->getColumnDimension(Coordinate::stringFromColumnIndex($col))
                ->setAutoSize(true);
        }

        $xlsDumper = new Xlsx($excel);
        $pFilename = @tempnam(sys_get_temp_dir(), 'phpxltmp');
        $xlsDumper->save($pFilename);
        $contents = file_get_contents($pFilename);
        @unlink($pFilename);

        unset($excel);
        unset($xlsDumper);

        return $contents;
    }

    /**
     * Get default style
     * @return array
     */
    protected function getDefaultStyleArray()
    {
        return [
            'font' => ['name' => 'Arial', 'size' => 9],
        ];
    }

    /**
     * Get default style for a filled cell
     * @return array
     */
    protected function getDefaultStyleForFilledCells()
    {
        return array_merge_recursive(
            $this->getDefaultStyleArray(),
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]
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
                $style['fill'] = [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => $bgColor],
                ];
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
            [
                'borders' => [
                    'bottom' => [
                        'style' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD0D0D0']
                ],
                'font' => ['bold' => true],
            ]
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
            [
                'borders' => [
                    'top' => [
                        'style' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD0D0D0'],
                ],
                'font' => ['bold' => true],
            ]
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
     * @param Worksheet $sheet      The worksheet
     * @param integer             $row        The selected row
     * @param array               $values     The values to insert
     * @param array               $formats    Associative arrays with formats
     * @param array               $styleArray An array representing the style
     * @throws \Exception
     */
    protected function applyValues(Worksheet $sheet, $row, $values, $formats, $styleArray = [])
    {
        $col = 0;
        foreach ($values as $index => $value) {
            $this->applyValue($sheet, $col, $row, $value, array_key_exists($index, $formats) ? $formats[$index] : Format::STRING, $styleArray);
            $col++;
        }
    }

    /**
     * Set value in specific cell
     * @param Worksheet $sheet      The worksheet
     * @param integer             $col        The selected column
     * @param integer             $row        The selected row
     * @param array               $value      The values to insert
     * @param array               $format     Associative arrays with formats
     * @param array               $styleArray An array representing the style
     * @throws \Exception
     */
    protected function applyValue(Worksheet $sheet, $col, $row, $value, $format, $styleArray = [])
    {
        if (0 == count($styleArray)) {
            $styleArray = $this->getDefaultStyleArrayForRow($row);
        }

        $style = new Style();
        $style->applyFromArray($styleArray);

        switch ($format) {
            case Format::DATE:
                if (!($value instanceof \DateTime)) {
                    $date = new \DateTime($value);
                } else {
                    $date = $value;
                }
                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
                break;

            case Format::DATETIME:
                if (!($value instanceof \DateTime)) {
                    $date = new \DateTime($value);
                } else {
                    $date = $value;
                }
                $value = Date::PHPToExcel($date);
                $style->getNumberFormat()->setFormatCode(self::FORMAT_DATETIME);
                break;

            case Format::FLOAT2:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                break;

            case Format::INTEGER:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                break;

            case Format::MONEY:
            case Format::MONEY2:
                $style->getNumberFormat()->setFormatCode(self::FORMAT_EUR);
                break;

            case Format::PCT:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                break;

            case Format::PCT2:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                break;

            case Format::STRING:
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                break;
        }

        $sheet->setCellValueByColumnAndRow($col, $row, $value);
        $sheet->duplicateStyle($style, Coordinate::stringFromColumnIndex($col).$row);
    }

    public function getMimeType()
    {
        return 'application/vnd.ms-office; charset=binary';
    }
}
