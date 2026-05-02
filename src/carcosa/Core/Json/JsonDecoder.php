<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json;

/**
 * A class to decode JSON (enforcing Exceptions on failure).
 */
class JsonDecoder
{

    /**
     * Decode a JSON string to a variable.
     * @param string $text The JSON-encoded text to decode.
     * @param bool $associative If true, decode objects to associative arrays.
     * @return mixed
     * @throws \JsonException If JSON-decoding fails.
     */
    public function __invoke(string $text, bool $associative = false)
    {
        return \json_decode($text, $associative, 512, JSON_THROW_ON_ERROR);
    }

}
