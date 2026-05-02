<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Encodings;

use Carcosa\Core\I18n\AbstractEncoding;

/**
 * A class that represents the ISO-8859-1 (Latin-1) character encoding.
 */
class Latin1 extends AbstractEncoding
{
    
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setName("ISO-8859-1")
            ->setIsFixedLength(true)
            ->setMaxBytesPerCharacter(1);
    }
    
}
