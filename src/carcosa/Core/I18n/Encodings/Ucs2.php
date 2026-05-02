<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Encodings;

use Carcosa\Core\I18n\AbstractEncoding;

/**
 * A class that represents the UCS-2 character encoding.
 */
class Ucs2 extends AbstractEncoding
{
    
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setName("UCS-2")
            ->setIsFixedLength(true)
            ->setMaxBytesPerCharacter(2);
    }
    
}
