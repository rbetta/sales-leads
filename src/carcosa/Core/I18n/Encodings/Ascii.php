<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Encodings;

use Carcosa\Core\I18n\AbstractEncoding;

/**
 * A class that represents the ASCII character encoding.
 */
class Ascii extends AbstractEncoding
{
    
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setName("ASCII")
            ->setIsFixedLength(true)
            ->setMaxBytesPerCharacter(1);
    }
    
}
