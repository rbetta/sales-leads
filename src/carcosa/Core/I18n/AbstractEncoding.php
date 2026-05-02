<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

/**
 * An abstract class that all character encodings are based on.
 */
abstract class AbstractEncoding implements iEncoding
{
    
    /**
     * The encoding name.
     * @var string
     */
    private string $name;
    
    /**
     * Whether this encoding uses fixed-length characters.
     * @var bool
     */
    private bool $isFixedLength;
    
    /**
     * The maximum byte length per character.
     * @var int
     */
    private int $maxBytesPerCharacter;
    
    /**
     * Construct an instance of this class.
     * @param string $name The encoding name.
     * @param bool $isFixedLength Whether all characters use a fixed byte
     * width in this encoding.
     * @param int $maxBytesPerCharacter The maximum number of bytes
     * per character in this encoding.
     * @throws \InvalidArgumentException If an empty string was supplied
     * as the encoding name.
     * @throws \InvalidArgumentException If a non-positive maximum number
     * of bytes per character was supplied.
     */
    public function __construct(string $name, bool $isFixedLength, int $maxBytesPerCharacter)
    {
        
        // Sanity checks.
        if ('' === $name) {
            throw new \InvalidArgumentException(
                "No encoding name was supplied to " . __METHOD__
            );
        }
        if ($maxBytesPerCharacter <= 0) {
            throw new \InvalidArgumentException(
                "The non-positive maximum number of bytes per " .
                "character $maxBytesPerCharacter was supplied to " .
                __METHOD__
            );
        }
        
        $this
            ->setName($name)
            ->setIsFixedLength($isFixedLength)
            ->setMaxBytesPerCharacter($maxBytesPerCharacter);
    }
    
    /**
     * Set the encoding name.
     * @param string $name
     * @return $this
     */
    private function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get the encoding name.
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Set whether this encoding uses fixed-length characters.
     * @param bool $isFixedLength
     * @return $this
     */
    private function setIsFixedLength(bool $isFixedLength) : self
    {
        $this->isFixedLength = $isFixedLength;
        return $this;
    }
    
    /**
     * Get whether this encoding uses fixed-length characters.
     * @return bool
     */
    public function getIsFixedLength() : bool
    {
        return $this->isFixedLength;
    }
    
    /**
     * Set the maximum number of bytes per character.
     * @param int $maxBytes
     * @return $this
     */
    private function setMaxBytesPerCharacter(int $maxBytes) : self
    {
        $this->maxBytesPerCharacter = $maxBytes;
        return $this;
    }
    
    /**
     * Get the maximum number of bytes per character.
     * @return int
     */
    public function getMaxBytesPerCharacter() : int
    {
        return $this->maxBytesPerCharacter;
    }
    
}
