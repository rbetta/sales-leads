<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n\Encodings;

use Carcosa\Core\I18n\AbstractEncoding;

/**
 * A class that represents the UTF-8 character encoding.
 */
class Utf8 extends AbstractEncoding
{
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setName("UTF-8")
            ->setIsFixedLength(false)
            ->setMaxBytesPerCharacter(4);
    }
    
}
