<?php

namespace App\Application\Service\Converter;

class JsonStringToArrayConverter
{
    public static function convert(string $json): array
    {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Invalid JSON provided: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $decoded;
    }
}