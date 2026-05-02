<?php
declare(strict_types = 1);
namespace Carcosa\Core\Regex;

/**
 * A factory for creating Regex instances.
 */
class RegexFactory
{

    /**
     * Create a Regex instance.
     * @param string $pattern The regular expresion pattern.
     * @throws \InvalidArgumentException If an empty string is provided.
     */
    public function create(string $pattern) : Regex
    {
        return new Regex($pattern);
    }

}
