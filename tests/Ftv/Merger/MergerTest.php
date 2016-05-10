<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 France Télévisions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Ftv\Tests\Merger;

use Ftv\Merger\Merger;

class MergerTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        // some rule will return some value as array, business is in rule
        $rule = $this->getMock('Ftv\Merger\Rule\RuleInterface');
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
