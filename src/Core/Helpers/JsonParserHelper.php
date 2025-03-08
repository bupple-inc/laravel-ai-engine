<?php

namespace BuppleEngine\Core\Helpers;

class JsonParserHelper
{
    /**
     * Parse a JSON string.
     *
     * @param string $json
     * @return array|null
     */
    public function parse(string $json): ?array
    {
        $parsedJson = str_replace(['\n', '\r'], '', $json);
        $parsedJson = preg_replace('/,\s*}/', '}', $parsedJson); // Remove trailing commas
        $parsedJson = preg_replace('/,\s*]/', ']', $parsedJson); // Remove trailing commas
        $parsedJson = preg_replace('/\s+/', ' ', $parsedJson); // Remove extra spaces
        $parsedJson = preg_replace('/\s*:\s*/', ':', $parsedJson); // Remove spaces around colons
        $parsedJson = preg_replace('/\s*,\s*/', ',', $parsedJson); // Remove spaces around commas
        $parsedJson = preg_replace('/\s*{\s*/', '{', $parsedJson); // Remove spaces around opening braces
        $parsedJson = preg_replace('/\xc2\xa0/', '', $parsedJson); // For gpt-4-mini
        $parsedJson = preg_replace('/\s+/', ' ', $parsedJson);

        preg_match('/```JSON(?<data>.*?)```/is', $parsedJson, $matches);

        return isset($matches['data'])
            ? json_decode($matches['data'], true)
            : json_decode($parsedJson, true);
    }
}
