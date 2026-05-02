<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json\Adapters;

use Carbon\Carbon;
use Carcosa\Core\Json\iJsonAdapter;

/**
 * An interface used to convert a Carbon instance into JSON, and
 * to instantiate it from JSON.
 */
class CarbonJsonAdapter implements iJsonAdapter
{

    /**
     * The date format used for JSON conversion.
     * @var string
     * @see date_format()
     */
    public const FORMAT = DATE_ISO8601;  // ISO 8601 date format.
    
    /**
     * Decode a JSON data structure (decoded from JSON text) to a Carbon
     * instance.
     * @param ?string $value The representative value decoded from JSON text.
     * @return ?Carbon The Carbon object represented by the decoded JSON data.
     * @throws \JsonException If JSON-decoding fails.
     */
    public function fromJsonValue($value)
    {
        if (null === $value) {
            return null;
        }
        return Carbon::createFromFormat(self::FORMAT, $value);
    }

    /**
     * Encode a Carbon instance to a JSON data structure (suitable for
     * conversion into JSON text).
     * @param ?Carbon $instance An instance to convert into JSON.
     * @return ?string The representative value suitable for encoding as JSON
     * text.
     */
    public function toJsonValue($instance)
    {
        if (null === $instance) {
            return null;
        }
        return $instance->format(self::FORMAT);
    }
    
}
