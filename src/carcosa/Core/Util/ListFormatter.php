<?php
declare(strict_types=1);
namespace Carcosa\Core\Util;

/**
 * A class that converts an array of values into a formatted list.
 */
class ListFormatter {

    /**
     * The list delimiter.
     * @var string
     */
    private string $delimiter = ', ';

    /**
     * The opening enclosure string for each value.
     * @var string
     */
    private string $enclosureStart = '"';

    /**
     * The closing enclosure string for each value.
     * @var string
     */
    private string $enclosureEnd = '"';

    /**
     * The string to use to indicate a null value.
     * @var string
     */
    private string $stringForNull = 'null';
    
    /**
     * The string to use to indicate a Boolean true value.
     * @var string
     */
    private $stringForBooleanTrue = 'true';
    
    /**
     * The string to use to indicate a Boolean false value.
     * @var string
     */
    private $stringForBooleanFalse = 'false';
    
    /**
     * Validate a value in the list.
     * @param mixed $value
     * @throws \RuntimeException If any value in the array is not one of
     * the following: scalar, null, or an object that implements \Stringable.
     * @return $this
     */
    private function validateValue($value) : self
    {
        if (! (
            is_scalar($value) ||
            null === $value ||
            $value instanceof \Stringable
        )) {
            $type = get_debug_type($value);
            throw new \RuntimeException(
                "An invalid value of type $type was supplied to " .
                __METHOD__ . " (expected: scalar, null, or \\Stringable)"
            );
        }

        return $this;
    }

    /**
     * Format a list of values.
     * @param array $values A list of values.
     * @return string
     * @throws \RuntimeException If any value in the array is not one of
     * the following: scalar, null, or an object that implements \Stringable.
     */
    public function format(array $values) : string
    {

        // Get the enclosure strings.
        $open   = $this->getEnclosureStart();
        $close  = $this->getEnclosureEnd();
        
        // Get the strings to indicate Boolean values.
        $true   = $this->getStringForBooleanTrue();
        $false  = $this->getStringForBooleanFalse();

        // Enclose all values in the array.
        $values = array_map(function ($value) use ($open, $close) {

            // Validate this value.
            $this->validateValue($value);

            // Format the value for display.
            if (is_bool($value)) {

                // Boolean values should not be enclosed, since
                // we don't want to misidentify them as strings.
                //
                // Note that we handle Boolean values specially
                // here because PHP's type juggling rules will
                // convert Boolean false to an empty string,
                // which is rarely desirable.
                return $value ? $true : $false;

            } elseif (null === $value) {
                
                // Null values should not be enclosed, since
                // we don't want to misidentify them as strings.
                // Display a suitable string to represent a null
                // value instead.
                return $this->getStringForNull();
            
            } else {
                
                // Enclose the value.
                return $open . $value . $close;
                
            }

        }, $values);

        // Return the enclosed values as a delimited list.
        return implode($this->getDelimiter(), $values);
    }

    /**
     * Set the delimiter string to use between values in the list.
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter(string $delimiter) : self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * Get the delimiter string to use between values in the list.
     * @return string
     */
    public function getDelimiter() : string
    {
        return $this->delimiter;
    }

    /**
     * Set the string to use when enclosing a value, if it is the
     * same for the opening and closing of the enclosure.
     * @pparam string $enclosure
     * @return $this 
     */
    public function setEnclosure(string $enclosure) : self
    {
        return $this
            ->setEnclosureStart($enclosure)
            ->setEnclosureEnd($enclosure);
    }
    
    /**
     * Set the string to use when opening an enclosure around each value
     * in the list.
     * @param string $opening
     * @return $this
     */
    public function setEnclosureStart(string $opening) : self
    {
        $this->enclosureStart = $opening;
        return $this;
    }

    /**
     * Get the string to use when opening an enclosure around each value
     * in the list.
     * @return string
     */
    public function getEnclosureStart() : string
    {
        return $this->enclosureStart;
    }

    /**
     * Set the string to use when closing an enclosure around each value
     * in the list.
     * @param string $closing
     * @return $this
     */
    public function setEnclosureEnd(string $closing) : self
    {
        $this->enclosureEnd = $closing;
        return $this;
    }

    /**
     * Get the string to use when closing an enclosure around each value
     * in the list.
     * @return string
     */
    public function getEnclosureEnd() : string
    {
        return $this->enclosureEnd;
    }

    /**
     * Set the string to use to indicate a null value.
     * @param string $value
     * @return $this
     */
    public function setStringForNull(string $value) : self
    {
        $this->stringForNull = $value;
        return $this;
    }
    
    /**
     * Get the string to use to indicate a null value.
     * @return string
     */
    public function getStringForNull() : string
    {
        return $this->stringForNull;
    }
    
    /**
     * Set the string to use to indicate a Boolean true value.
     * @param string $value
     * @return $this
     */
    public function setStringForBooleanTrue(string $value) : self
    {
        $this->stringForBooleanTrue = $value;
        return $this;
    }
    
    /**
     * Get the string to use to indicate a Boolean true value.
     * @return string
     */
    public function getStringForBooleanTrue() : string
    {
        return $this->stringForBooleanTrue;
    }
    
    /**
     * Set the string to use to indicate a Boolean false value.
     * @param string $value
     * @return $this
     */
    public function setStringForBooleanFalse(string $value) : self
    {
        $this->stringForBooleanFalse = $value;
        return $this;
    }
    
    /**
     * Get the string to use to indicate a Boolean false value.
     * @return string
     */
    public function getStringForBooleanFalse() : string
    {
        return $this->stringForBooleanFalse;
    }
    
}