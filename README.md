stats-table
===========

PHP Library to handle statistics tables and CSV, JSON and Excel exports. [![Build Status](https://travis-ci.org/igraal/stats-table.png?branch=master)](https://travis-ci.org/igraal/stats-table)

Summary
-------

This library helps you create statistical tables given some data. You provide data, headers and what you want for the footer line, and then you can dump your table into a JSON, CSV or Excel file.

This is very useful to manipulate a lot of tables you want to see in an HTML FrontOffice and when you want to add the ability to get this data in CSV or Excel File as well.

Installation
------------

### Using composer

Using composer, just add the following require to your composer.json :

    "require": {
        ... ,
        "igraal/stats-table": "dev-master"
    }

Usage
-----

### Using the class StatsTable

The class `StatsTable` is the class that will hold your data. It takes one mandotary arguments, and 4 options arguments. The simpler way to create a new table is to pass the data itself and its headers (even if headers are optional).

```php
use IgraalOSL\StatsTable\StatsTable;

$data = [
    ['date' => '2014-01-01', 'hits' => 32500],
    ['date' => '2014-01-02', 'hits' => 48650],
];
$headers = ['date' => 'Date', 'hits' => 'Number of hits'];
$statsTable = new StatsTable($data, $headers);
```

### Dumping a table

Three formats are currently supported : Excel, CSV and JSON. Thus, you can use the same table with your ajax calls or to be downloaded.

First, create your dumper, then dump your data.

```php
use IgraalOSL\StatsTable\Dumper\Excel\ExcelDumper;

$excelDumper = new ExcelDumper();
$excelContents = $excelDumper->dump($statsTable);

header('Content-type: application/vnd.ms-excel');
echo $excelContents
```

### Using stats table builder

To help you construct a table, you can use the `StatsTableBuilder` class. It helps you combine data from multiple tables, and can create automatic calculated columns. It also helps you build aggregations (aka the footer line), with multiple possibilities : ratio, sum, average or static content.

```php
use IgraalOSL\StatsTable\StatsTableBuilder;

$data = [
    '2014-01-01' => ['hits' => 32500],
    '2014-01-02' => ['hits' => 48650],
];

$statsTableBuilder = new StatsTableBuilder(
    $data,
    ['hits' => 'Number of hits']
);
$statsTableBuilder->addIndexesAsColumn('date', 'Date');

$statsTable = $statsTableBuilder->build();
```
