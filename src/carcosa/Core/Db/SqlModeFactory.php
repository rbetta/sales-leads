<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Illuminate\Database\ConnectionInterface;

/**
 * A factory class for creating an SqlMode instance.
 * @author Randall Betta
 *
 */
class SqlModeFactory
{
    
    /**
     * Create an SqlMode instance for the default database connection.
     * @return SqlMode
     * @throws \RuntimeException If the SQL mode cannot be determined. (This
     * should never happen.)
     */
    public function createByDefaultConnection() : SqlMode
    {
        return $this->createByConnection(DB::connection());
    }
    
    /**
     * Create an SqlMode instance for a database connection.
     * @param ConnectionInterface $connection The database connection.
     * @return SqlMode
     * @throws \RuntimeException If the SQL mode cannot be determined. (This
     * should never happen.)
     */
    public function createByConnection(ConnectionInterface $connection) : SqlMode
    {
        // Obtain the server's SQL mode. The result will be a comma-delimited
        // list of SQL option names that are enabled on the database server.
        $sql = "SELECT @@sql_mode AS sql_mode";
        $row = collect($connection->select($sql))
            ->first();
        
        // Sanity check; the previous query must return a result.
        if (null === $row) {
            throw new \RuntimeException(
                "The SQL mode could not be determined by " . __METHOD__
            );
        }
        
        // Parse the SQL mode's currently enabled options.
        $enabledOptions = explode(",", $row['sql_mode']);
        
        // Create the SqlMode instance.
        return new SqlMode($enabledOptions);
        
    }

}
