<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

/**
 * A class that escapes a database identifier name.
 * @author Randall Betta
 *
 */
class DbIdentifier
{
    
    /**
     * The SQL mode.
     * @var SqlMode
     */
    private SqlMode $sqlMode;
    
    /**
     * Construct an instance of this class.
     * SqlMode $sqlMode The SQL mode.
     */
    public function __construct(SqlMode $sqlMode)
    {
        $this->sqlMode = $sqlMode;
    }
    
    /**
     * Get the SQL mode.
     * @return SqlMode
     */
    public function getSqlMode() : SqlMode
    {
        return $this->sqlMode;
    }
    
    /**
     * Get whether to use ANSI quotes.
     * @return bool
     */
    public function getUseAnsiQuotes() : bool
    {
        $sqlMode = $this->getSqlMode();
        return $sqlMode->hasEnabledOption(SqlMode::OPTION_ANSI_QUOTES);
    }
    
    /**
     * Quote a database identifier.
     * @param string $identifier A database identifier (such as a field name).
     * @return string The quoted database identifier.
     */
    public function __invoke(string $identifier) : string
    {
        
        // Decompose the identifier. (Its schema name and table name may be
        // present, delimited by periods.)
        $parts = explode(".", $identifier);
        
        // Determine how to quote each component, based on the SQL mode.
        $quote = $this->getUseAnsiQuotes() ? '"' : '`';
        
        // Return the quoted identifier.
        return array_map(
            fn($value) => ($quote . $value . $quote),
            $parts
        );
    }
    
}
