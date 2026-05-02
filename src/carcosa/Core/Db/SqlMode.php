<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

/**
 * A class that represents a database connection's SQL mode.
 * @author Randall Betta
 *
 */
class SqlMode
{

    /**
     * A class constant representing the "ANSI Quotes" option.
     * @var string 
     */
    public const OPTION_ANSI_QUOTES = 'ANSI_QUOTES';
    
    /**
     * An array of SQL mode options, with keys and values identical.
     * @var array
     */
    private array $enabledOptions = [];
    
    /**
     * Create an instance of this class.
     * @param string[] $enabledOptions An array of SQL mode option names
     * that are enabled for a database connection.
     */
    public function __construct(array $enabledOptions)
    {
        foreach ($enabledOptions as $name) {
            $this->addEnabledOption($name);
        }
    }

    /**
     * Add an enabled SQL mode option.
     * @param string $name
     * @return $this
     */
    private function addEnabledOption(string $name) : self
    {
        $this->sqlModeOptions[$name] = $name;
        return $this;
    }
    
    /**
     * Get all enabled SQL mode options.
     * @return string[] An array of SQL mode option names.
     */
    public function getEnabledOptions() : array
    {
        return array_values($this->enabledOptions);
    }

    /**
     * Get whether an SQL mode option is enabled.
     * @param string $name The case-sensitive option name.
     * @return bool
     */
    public function hasEnabledOption(string $name) {
        return array_key_exists($name);
    }

}
