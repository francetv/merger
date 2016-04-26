<?php

namespace Ftv\Merger\Rule;

/**
 * A merge rule to apply when merging video data
 */
interface RuleInterface
{
    /**
     * @param array $merged
     * @param array $firstSource
     * @param array $secondSource
     *
     * @return array
     */
    public function apply(array $merged, array $firstSource, array $secondSource);
}
