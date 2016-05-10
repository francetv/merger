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

namespace Ftv\Merger;

use Ftv\Merger\Rule\RuleInterface;

/**
 * Apply rules to merge to arrays
 */
class Merger
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param array $merged
     * @param array $firstSource
     * @param array $secondSource
     *
     * @return array
     */
    public function merge(array $merged, array $firstSource, array $secondSource)
    {
        $merged = array_replace_recursive(
            $merged,
            $this->arrayFilterRecursive($firstSource),
            $this->arrayFilterRecursive($secondSource)
        );

        /** @var RuleInterface $rule */
        foreach ($this->rules as $rule) {
            $merged = $rule->apply($merged, $firstSource, $secondSource);
        }

        return $merged;
    }

    /**
     * Filter recursively array
     *
     * @param array $input
     *
     * @return array
     */
    private function arrayFilterRecursive($input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->arrayFilterRecursive($value);
            }
        }

        return array_filter($input);
    }
}
