<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json;

/**
 * A class to encode JSON (enforcing Exceptions on failure).
 */
class JsonEncoder
{

    /**
     * Whether to pretty-print the JSON output.
     * @var bool
     */
    private bool $prettyPrinting = false;

    /**
     * Encode a value as a JSON string.
     * @param mixed $value The value to encode.
     * @return string The JSON-formatted output.
     * @throws \JsonException If JSON-encoding fails.
     */
    public function __invoke($value) : string
    {
        // Set JSON encoding flags.
        $flags = JSON_THROW_ON_ERROR;
        if ($this->getPrettyPrinting()) {
            $flags = $flags | JSON_PRETTY_PRINT;
        }

        // Encode the value to JSON.
        return \json_encode($value, $flags);
    }

    /**
     * Set whether to use pretty-printed output.
     * @param bool $prettyPrint
     * @return $this
     */
    public function setPrettyPrinting(bool $prettyPrint) : self
    {
        $this->prettyPrinting = $prettyPrint;
        return $this;
    }

    /**
     * Get whether to use pretty-printed output.
     * @return bool
     */
    public function getPrettyPrinting() : bool
    {
        return $this->prettyPrinting;
    }

}
