<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json\Adapters;

use Carcosa\Core\I18n\iLocale;
use Carcosa\Core\I18n\LocaleFactory;
use Carcosa\Core\Json\iJsonAdapter;

/**
 * An interface used to convert a locale class instance
 * (implementing the iLocale interface) into JSON, and
 * to instantiate it from JSON.
 */
class LocaleJsonAdapter implements iJsonAdapter
{

    /**
     * Decode a JSON data structure (decoded from JSON text) to an iLocale
     * instance.
     * @param ?string $value The representative value decoded from JSON text.
     * @return ?iLocale The locale object represented by the decoded JSON data.
     * @throws \JsonException If JSON-decoding fails.
     */
    public function fromJsonValue($value)
    {
        if (null === $value) {
            return null;
        }
        $localeFactory = \App::make(LocaleFactory::class);
        return $localeFactory->create($value);
    }

    /**
     * Encode an iLocale instance to a JSON data structure (suitable for
     * conversion into JSON text).
     * @param ?iLocale $instance An instance to convert into JSON.
     * @return ?string The representative value suitable for encoding as JSON
     * text.
     */
    public function toJsonValue($instance)
    {
        if (null === $instance) {
            return null;
        }
        return (string) $instance;
    }
    
}
