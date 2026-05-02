<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

/**
 * An interface that represents a character encoding.
 */
interface iEncoding
{
    
    /**
     * Get the encoding name.
     * @return string
     */
    public function getName() : string;
    
    /**
     * Get whether this encoding uses fixed-length characters.
     * @return bool
     */
    public function getIsFixedLength() : bool;
    
    /**
     * Get the maximum number of bytes per character.
     * @return int
     */
    public function getMaxBytesPerCharacter() : int;
    
}
