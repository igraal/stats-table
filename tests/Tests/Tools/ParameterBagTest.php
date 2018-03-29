<?php

namespace Tests\Tools;

use IgraalOSL\StatsTable\Tools\ParameterBag;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    private function getSampleData()
    {
        return [
            '1' => 'One',
            '2' => 'Two',
        ];
    }

    private function getSampleBag()
    {
        return new ParameterBag($this->getSampleData());
    }

    public function testConstructorWithArray()
    {
        $data = $this->getSampleData();
        $bag = $this->getSampleBag();

        $this->assertEquals($data, $bag->toArray());
    }

    public function testConstructorWithParameterBag()
    {
        $data = $this->getSampleData();
        $bag = $this->getSampleBag();

        $bag2 = new ParameterBag($bag);
        $this->assertEquals($data, $bag2->toArray());
    }

    public function testHasKey()
    {
        $bag = $this->getSampleBag();

        $this->assertTrue($bag->has('1'));
        $this->assertFalse($bag->has('3'));
    }

    public function testGet()
    {
        $bag = $this->getSampleBag();

        $this->assertEquals('One', $bag->get('1', 'One value'));
        $this->assertEquals('Three', $bag->get('3', 'Three'));
    }
}
