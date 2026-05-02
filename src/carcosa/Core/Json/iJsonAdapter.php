<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json;

/**
 * An interface used to convert a general-purpose class into JSON, and
 * to instantiate it from JSON.
 */
interface iJsonAdapter
{

    /**
     * Decode a JSON data structure (decoded from JSON text) to a PHP type.
     * @param mixed $value The representative value decoded from JSON text.
     * @return object The actual, intended PHP type created from JSON.
     * @throws \JsonException If JSON-decoding fails.
     */
    public function fromJsonValue($value);

    /**
     * Encode a PHP data type to a JSON data structure (suitable for
     * conversion into JSON text).
     * @param mixed $value A PHP data type value to convert into JSON.
     * @return mixed The representative value suitable for encoding as JSON
     * text.
     */
    public function toJsonValue($value);
    
}
