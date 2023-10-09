<?php

if (!function_exists('cleanTags'))  {
    /**
     * Clean Tags
     * Leaving input with
     *  - lowercase and uppercase letters
     *  - numbers
     *  - hyphens
     *  - underscores
     *  - spaces
     *
     * @param array $tags
     * @return array
     */
    function cleanTags($tags) {
        if (!$tags || !count($tags)) {
            return null;
        }

        $cleaned = collect($tags)->map(fn ($tag) => preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $tag));

        return $cleaned->toArray();
    }
}

if (!function_exists('cleanRequestQueryParams'))  {
    function cleanRequestQueryParams($params) {
        $data = collect($params)->filter(fn($param) => !is_null($param) && $param != '');

        return $data;
    }
}
