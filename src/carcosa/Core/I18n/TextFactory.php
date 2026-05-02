<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

/**
 * A factory for creating Text instances.
 */
class TextFactory
{

    /**
     * Create a new Text instance.
     * @param string $text The text to store. This can be any supported
     * encoding.
     * @param iEncoding $encoding The character encoding of the text.
     * This can be any supported encoding, including multi-byte and
     * variable-length encodings.
     * @return Text
     */
    public function create(string $text, iEncoding $encoding) : Text
    {
        return new Text($text, $encoding);
    }
    
    /**
     * Create a new Text instance using the UTF-8 encoding.
     * @param string $text UTF-8 encoded text.
     * @return Text
     */
    public function createUtf8(string $text) : Text
    {
        $encodingFactory = \App::make(EncodingFactory::class);
        return $this->create($text, $encodingFactory->createUtf8());
    }
    
    /**
     * Create a new Text instance using the ASCII encoding.
     * @param string $text Ascii encoded text.
     * @return Text
     */
    public function createAscii(string $text) : Text
    {
        $encodingFactory = \App::make(EncodingFactory::class);
        return $this->create($text, $encodingFactory->createAscii());
    }
    
}
