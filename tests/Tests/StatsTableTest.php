<?php

namespace Tests;

use IgraalOSL\StatsTable\StatsTable;

class StatsTableTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveColumn()
    {
        $statsTable = new StatsTable(
            array(
                array('a' => 'a', 'b' => 'b'),
                array('a' => 'A', 'b' => 'B')
            ),
            array('a' => 'Alpha', 'b' => 'Bravo')
        );

        $statsTable->removeColumn('b');

        $this->assertEquals(array('a' => 'Alpha'), $statsTable->getHeaders());

        $this->assertEquals(
            array(
                array('a' => 'a'),
                array('a' => 'A')
            ),
            $statsTable->getData()
        );
    }

    private function _getSimpleTestData()
    {
        return new StatsTable(
            array(
                array('name' => 'Pierre', 'age' => '32'),
                array('name' => 'Jacques', 'age' => '28'),
                array('name' => 'Jean', 'age' => '32'),
                array('name' => 'Paul', 'age' => '25'),
            ),
            array('name' => 'Name', 'age' => 'Age', 'order'=>'Order')
        );
    }


    /**
     * @dataProvider dataProviderForOneColumn
     */
    public function testSortOneColumn($columnName, $asc, $expected)
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortColumn($columnName, $asc);
        $this->assertSame($expected, $statsTable->getData());
    }


    public function dataProviderForOneColumn()
    {
        return array(
            array(
                'age', true,
                array(
                    3=>array('name' => 'Paul', 'age' => '25'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                )
            ),
            array(
                'name', true,
                array(
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                    3=>array('name' => 'Paul', 'age' => '25'),
                    0=>array('name' => 'Pierre', 'age' => '32'),
                )
            ),
            array(
                'age', false,
                array(
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    3=>array('name' => 'Paul', 'age' => '25'),
                )
            ),
            array(
                'name', false,
                array(
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    3=>array('name' => 'Paul', 'age' => '25'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                )
            )
        );
    }

    /**
     * @dataProvider dataProviderForMultipleColumn
     */
    public function testSortMultipleColumn($params, $expected)
    {
        $statsTable = $this->_getSimpleTestData();
        $statsTable->sortMultipleColumn($params);
        $this->assertSame($expected, $statsTable->getData());
    }

    public function dataProviderForMultipleColumn()
    {
        return array(
            array(
                array('age'=>true,'name'=>true),
                array(
                    3=>array('name' => 'Paul', 'age' => '25'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                    0=>array('name' => 'Pierre', 'age' => '32'),
                )
            ),
            array(
                array('age'=>true,'name'=>false),
                array(
                    3=>array('name' => 'Paul', 'age' => '25'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                )
            ),
            array(
                array('age'=>false,'name'=>true),
                array(
                    2=>array('name' => 'Jean', 'age' => '32'),
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    3=>array('name' => 'Paul', 'age' => '25'),
                )
            ),
            array(
                array('age'=>false,'name'=>false),
                array(
                    0=>array('name' => 'Pierre', 'age' => '32'),
                    2=>array('name' => 'Jean', 'age' => '32'),
                    1=>array('name' => 'Jacques', 'age' => '28'),
                    3=>array('name' => 'Paul', 'age' => '25'),
                )
            )
        );
    }


    private function _getAdvancedTestData()
    {
        return new StatsTable(
            array(
                array('name' => 'Pierre', 'age' => '32', 'order'=>array('nb'=>10,'id'=>'4587956')),
                array('name' => 'Jacques', 'age' => '28', 'order'=>array('nb'=>10,'id'=>'2479109')),
                array('name' => 'Jean', 'age' => '32', 'order'=>array('nb'=>1,'id'=>'9210367')),
                array('name' => 'Paul', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
                array('name' => 'Celine', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
            ),
            array('name' => 'Name', 'age' => 'Age', 'order'=>'Order')
        );
    }

    /**
     * @dataProvider dataProviderForMultipleColumnWithFunc
     */
    public function testSortMultipleColumnWithFunc($params, $expected)
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortMultipleColumn($params);
        $this->assertSame($expected, $statsTable->getData());
    }

    public function dataProviderForMultipleColumnWithFunc()
    {
        $customSort = function($a, $b){
            if($a['nb'] == $b['nb']) {
                if($a['id'] == $b['id']) {
                    return 0;
                }
                return $a['id'] < $b['id']  ? -1 :1;
            }

            return $a['nb'] < $b['nb']  ? -1 :1;
        };


        return array(
            array(
                array('order'=>$customSort, 'name'=>'strcmp'),
                array(
                    2=>array('name' => 'Jean', 'age' => '32', 'order'=>array('nb'=>1,'id'=>'9210367')),
                    1=>array('name' => 'Jacques', 'age' => '28', 'order'=>array('nb'=>10,'id'=>'2479109')),
                    0=>array('name' => 'Pierre', 'age' => '32', 'order'=>array('nb'=>10,'id'=>'4587956')),
                    4=>array('name' => 'Celine', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
                    3=>array('name' => 'Paul', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
                )
            )
        );
    }

    /**
     * @dataProvider dataProviderForOneColumnWithFunc
     */
    public function testSortOneColumnWithFunc($columnName, $customCompareFunc, $expected)
    {
        $statsTable = $this->_getAdvancedTestData();
        $statsTable->uSortColumn($columnName, $customCompareFunc);
        $this->assertSame($expected, $statsTable->getData());
    }

    public function dataProviderForOneColumnWithFunc()
    {
        $customSort = function($a, $b){
            if($a == $b) {
               return 0;
            }

            return $a < $b  ? 1 : -1;
        };

        return array(
            array(
                'age', $customSort,
                array(
                    0=>array('name' => 'Pierre', 'age' => '32', 'order'=>array('nb'=>10,'id'=>'4587956')),
                    2=>array('name' => 'Jean', 'age' => '32', 'order'=>array('nb'=>1,'id'=>'9210367')),
                    1=>array('name' => 'Jacques', 'age' => '28', 'order'=>array('nb'=>10,'id'=>'2479109')),
                    4=>array('name' => 'Celine', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
                    3=>array('name' => 'Paul', 'age' => '25', 'order'=>array('nb'=>24,'id'=>'5214680')),
                )
            )
        );
    }
}
