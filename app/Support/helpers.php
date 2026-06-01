<?php

if (! function_exists('array_all')) {
    /**
     * Return true if all items pass the truth test callback.
     */
    function array_all(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if (! $callback($value, $key)) {
                return false;
            }
        }

        return true;
    }
}
