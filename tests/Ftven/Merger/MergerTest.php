<?php

namespace Ftven\Tests\Merger;

use Ftven\Merger\Merger;

class MergerTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        // some rule will return some value as array, business is in rule
        $rule = $this->getMock('Ftven\Merger\Rule\RuleInterface');
        $rule->expects($this->once())
            ->method('apply')
            ->with(
                ['title' => 'b', 'program' => 'a', 'idPlurimedia' => 'a', 'idTaxo' => 'a', 'tags' => ['id' => 'e', 'label' => 'e']],
                ['title' => 'a', 'program' => 'a', 'idPlurimedia' => 'a', 'idTaxo' => 'a'],
                ['title' => 'b', 'program' => null]
            )
            ->will($this->returnValue(['title' => 'b', 'program' => 'a', 'tags' => ['id' => 'e', 'label' => 'e']]));

        $merger = new Merger();
        $merger->addRule($rule);

        $this->assertSame(
            ['title' => 'b', 'program' => 'a',  'tags' => ['id' => 'e', 'label' => 'e']],
            $merger->merge(
                ['title' => 'b', 'program' => 'a', 'idPlurimedia' => 'a', 'idTaxo' => 'a', 'tags' => ['id' => 'e', 'label' => 'e']],
                ['title' => 'a', 'program' => 'a', 'idPlurimedia' => 'a', 'idTaxo' => 'a'],
                ['title' => 'b', 'program' => null]
            )
        );
    }

    public function testArrayFilterRecursive()
    {
        $array = [
            "level10" => [
                "level20" => [
                    "level3" => null
                ],
                "level21" => "not empty",
                "level22" => [
                    "level31" => "not empty",
                    "level32" => null
                ],
                "level23" => null
            ],
            "level11" => "not empty",
            "level12" => null
        ];

        $expected = [
            "level10" => [
                "level21" => "not empty",
                "level22" => [
                    "level31" => "not empty",
                ],
            ],
            "level11" => "not empty",
        ];

        $merger = new Merger();

        $reflection = new \ReflectionClass(get_class($merger));
        $method = $reflection->getMethod("arrayFilterRecursive");
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($merger, [$array]));
    }
}
