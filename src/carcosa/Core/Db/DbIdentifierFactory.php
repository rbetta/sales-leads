<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Illuminate\Database\ConnectionInterface;

/**
 * A factory class for creating a DbIdentifier instance.
 * @author Randall Betta
 *
 */
class DbIdentifierFactory
{
    
    /**
     * Create a DbIdentifier instance for a given SQL mode.
     * @param SqlMode $sqlMode The connection's SQL mode.
     * @return DbIdentifier
     */
    public function createBySqlMode(SqlMode $sqlMode)
    {
        return new DbIdentifier($sqlMode);
    }
    
    /**
     * Create a DbIdentifier instance for a given database connection.
     * @param ConnectionInterface $connection
     * @return DbIdentifier
     */
    public function createByConnection(ConnectionInterface $connection) : DbIdentifier
    {
        $sqlModeFactory = \App::make(SqlModeFactory::class);
        $sqlMode = $sqlModeFactory->createByConnection($connection);
        return $this->createBySqlMode($sqlMode);
    }
    
    /**
     * Create a DbIdentifier instance for the default database connection.
     * @param ConnectionInterface $connection
     * @return DbIdentifier
     */
    public function createByDefaultConnection() : DbIdentifier
    {
        $sqlModeFactory = \App::make(SqlModeFactory::class);
        $sqlMode = $sqlModeFactory->createByDefaultConnection();
        return $this->createBySqlMode($sqlMode);
    }
    
}
