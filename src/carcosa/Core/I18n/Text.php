<?php
declare(strict_types = 1);
namespace Carcosa\Core\I18n;

use Carcosa\Core\I18n\Encodings\Utf8;

/**
 * A class that represents text in an arbitrary character encoding
 * (including multi-byte ones). 
 */
class Text implements \IteratorAggregate, \Stringable
{

    /**
     * The character encoding of the text represented by this instance.
     * @var iEncoding
     */
    private iEncoding $encoding;
    
    /**
     * The text represented by this instance.
     * @var string
     */
    private string $text;
    
    /**
     * The cached character count of the text.
     * @var int|null The integer character count of the text,
     * or null if this has not yet been calculated.
     */
    private int|null $charCount = null;
    
    /**
     * Construct an instance of this class.
     * @param string $text The text represented by this instance.
     * @param iEncoding $encoding The character encoding
     * of the text. This can be any supported encoding, including
     * multi-byte and variable-length encodings.
     */
    public function __construct(string $text, iEncoding $encoding)
    {
        
        // Note: set the encoding first, so we can support perform
        // any desired initial calculations on the text that may
        // require knowledge of the text's encoding (such as a
        // character count).
        $this
            ->setEncoding($encoding)
            ->setText($text);
        
    }
    
    /**
     * Set the character encoding of the text represented by this instance.
     * @param iEncoding $encoding
     * @return $this
     */
    private function setEncoding(iEncoding $encoding) : self
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get the character encoding of the text represented by this instance.
     * @return iEncoding The character encoding of the text. This can
     * be any supported encoding, including multi-byte and variable-length
     * encodings.
     */
    public function getEncoding() : iEncoding
    {
        return $this->encoding;
    }
    
    /**
     * Set the text represented by this instance.
     * @param string $text
     * @return $this
     */
    private function setText(string $text) : self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get the text represented by this instance.
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }
 
    /**
     * Get the character count of the text.
     * @return int
     */
    public function getCharCount() : int
    {
        
        // Initialize the text's character count, if this
        // has not yet been cached.
        if (null === $this->charCount) {
            $this->charCount = mb_strlen(
                $this->getText(),
                $this->getEncoding()
            );
        }
        
        return $this->charCount;
        
    }

    /**
     * Get the byte count of the text.
     * @return int
     */
    public function getByteCount() : int
    {
        return strlen($this->getText());
    }
    
    /**
     * Get the first zero-based instance of the given substring within
     * the text.
     * @param string|Text $search The text to search for. If the supplied
     * value is a string, then it is assumed to have the same character
     * encoding as this instance. If the supplied value is a Text instance,
     * then a copy of its text value will be converted to this instance's
     * encoding before searching (the supplied Text instance will remain
     * unchanged).
     * @param int $offset The zero-indexed character offset within this
     * instance's text to begin the search. If unspecified, then 0
     * will be used. Negative offsets search from the end of the text.
     * @return int|false The first zero-based index where the search
     * string is found, or false if it is not present.
     * @throws \RuntimeException If encoding conversion of the search
     * text to match this instance's encoding fails.
     */
    public function find(string|Text $search, int $offset) : int|false
    {
        
        $thisEncoding = $this->getEncoding();
        
        // Handle a supplied Text instance as the search string.
        if ($search instanceof Text) {
            
            // Convert the supplied search text into this instance's
            // encoding if they differ.
            if ($thisEncoding !== $search->getEncoding()) {
                $search = $search->toEncoding($thisEncoding);
            }
            
            // Convert the Text instance to a string, to simplify
            // all subsequent operations.
            $search = $search->getText();
            
        }
        
        return mb_strpos(
            $this->getText(),
            $search,
            $offset,
            $thisEncoding
        );
    }
    
    /**
     * Get a substring of this instance's text.
     * @param int $start The zero-based start character. If negative,
     * then the substring will begin the specified number of characters
     * from the end of the text.
     * @param int|null $maxLength The maximum length of the substring. If
     * unspecified or null, then all characters to the end of the string
     * will be returned.
     * @return string
     */
    public function substring(int $start, int|null $maxLength = null) : string
    {
        return mb_substr(
            $this->getText(),
            $start,
            $maxLength,
            $this->getEncoding()
        );
    }
    
    /**
     * Get a copy of this instance converted to the specified encoding.
     * @param string|IEncoding $encoding The desired encoding.
     * @return Text
     * @throws \RuntimeException If encoding conversion fails.
     */
    public function toEncoding(string|iEncoding $encoding) : self
    {
        
        // Convert a target string encoding into its appropriate
        // class that implements the iEncoding interface.
        if (is_string($encoding)) {
            $encodingFactory = \App::make(EncodingFactory::class);
            $encoding = $encodingFactory->createByName($encoding);
        }
        
        // DEGENERATE CASE:
        //
        // If we are not changing encodings, then just return
        // a clone of this instance.
        $thisEncoding = $this->getEncoding();
        if (
            // Encodings should be instantiated as singletons by the
            // framework, so we check memory location equivalence first
            // for speed. However, cloning operations or use of the
            // "new" operator can bypass this, so we compare encoding
            // names as a failsafe (using short-circuit evaluation).
            $encoding === $thisEncoding ||
            $encoding->getName() === $thisEncoding->getName()
        ) {
            return (clone $this);
        }
        
        // Obtain a string with the new encoding.
        $newText = mb_convert_encoding(
            $this->getText(),
            $encoding,
            $thisEncoding
        );
        
        // Handle any encoding conversion failure.
        if (false === $newText) {
            throw new \RuntimeException(
                "Failed to convert text to the \"$encoding\" encoding in " .
                __METHOD__
            );
        }
        
        // Create the new Text instance.
        $textFactory = \App::make(TextFactory::class);
        return $textFactory->create($newText, $encoding);
        
    }
    
    /**
     * Get this text as an array of single-character strings.
     * @return array An array where each element is a single character.
     * @throws \RuntimeException If the text is in a fixed-width
     * character encoding, but its length is not an exact multiple of
     * the number of bytes per character in that encoding.
     */
    public function toArray() : array
    {
        $encoding = $this->getEncoding();
        
        // How we split the text into its component characters will
        // differ based on whether the character encoding uses
        // fixed-length characters. Some methods are more efficient
        // than others.
        if ($encoding->getIsFixedLength()) {
            
            // Handle fixed-width character encodings by using
            // simple string offsets.
            $text           = $this->getText();
            $bytesPerChar   = $encoding->getMaxBytesPerCharacter();
            $result         = [];
            $length         = strlen($text);
            if ($length % $bytesPerChar !== 0) {
                
                // The text has an unexpected byte count.
                throw new \RuntimeException(
                    "Invoked " . __METHOD__ . " on text whose byte length " .
                    "was not a multiple of $bytesPerChar (actual length: " .
                    "$length bytes)"
                );
                
            }
            for ($i = 0; $i < $length; $i += $bytesPerChar) {
                $result[] = substr($text, $i, $bytesPerChar);
            }
            return $result;
            
        } else {
            
            // Handle variable-length encoding.
            if ($encoding instanceof Utf8) {
                
                // UTF-8 is efficiently handled using preg_split().
                $text = $this->getText();
                return preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY);
                
            } else {
                
                // Handle all other variable-length encodings in a non-optimal
                // but correct way.
                $length = $this->getCharCount();
                $result = [];
                for ($i = 0; $i < $length; $i++) {
                    $result[] = $this->substring($i, 1);
                }
                return $result;
                
            }
            
        }
        
    }
    
    /**
     * Get an iterator, so that this instance's text can be iterated
     * through character-by-character in a foreach statement. Each
     * resultant character will be a string (not a Text instance).
     * @return \Iterator
     */
    public function getIterator() : \Iterator
    {
        return new ArrayIterator($this->toArray());
    }
    
    /**
     * Implement the \Stringable interface, so this class can be
     * transparently converted into the text it contains in string
     * contexts.
     * @return string
     */
    public function __toString() : string
    {
        return $this->getText();
    }
    
}
