<?php

namespace Ftven\Merger;

use Ftven\Merger\Rule\RuleInterface;

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
