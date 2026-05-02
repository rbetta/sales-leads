<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

use Carcosa\Core\I18n\Ascii;
use Carcosa\Core\I18n\Latin1;
use Carcosa\Core\I18n\Ucs2;
use Carcosa\Core\I18n\Ucs4;
use Carcosa\Core\I18n\Utf8;
use Carcosa\Core\I18n\Utf16;
use Carcosa\Core\I18n\Utf32;

/**
 * A factory class for instantiating character encoding classes.
 */
class EncodingFactory
{

    /**
     * Create an encoding from its case-insensitive string name.
     * @param string $name The case-insensitive encoding name.
     * Some leeway is provided here (e.g. "utf8" vs. "utf-8").
     * @return AbstractEncoding The concrete subclass of
     * AbstractEncoding that matches the supplied name.
     * @throws \RuntimeException If no encoding matching the
     * supplied name was found.
     */
    public function createByName(string $name) : AbstractEncoding
    {
        // Normalize the encoding name.
        $normalizedName = strtolower($name);
        $normalizedName = str_replace("-", "", $normalizedName);
        
        // Attempt to find a matching AbstractEncoding subclass by name.
        $encoding = match ($normalizedName) {
            
            'ascii'     => \App::make(Ascii::class),
            'utf8'      => \App::make(Utf8::class),
            'utf16'     => \App::make(Utf16::class),
            'utf32'     => \App::make(Utf32::class),
            'ucs2'      => \App::make(Ucs2::class),
            'ucs4'      => \App::make(Ucs4::class),
            'iso88591'  => \App::make(Latin1::class),   // ISO-8859-1 is Latin-1.
            'latin1'    => \App::make(Latin1::class),
            default     => null,
            
        };
        
        // Throw an exception if the encoding is unknown.
        if (null === $encoding) {
            throw new \RuntimeException(
                "The unknown encoding \"$name\" was supplied to " . __METHOD__
            );
        }
        
        return $encoding;
        
    }
    
}
