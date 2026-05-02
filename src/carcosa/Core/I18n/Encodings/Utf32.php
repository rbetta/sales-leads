<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Encodings;

use Carcosa\Core\I18n\AbstractEncoding;

/**
 * A class that represents the UTF-32 character encoding.
 */
class Utf32 extends AbstractEncoding
{
    
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setName("UTF-32")
            ->setIsFixedLength(true)
            ->setMaxBytesPerCharacter(4);
    }
    
}
