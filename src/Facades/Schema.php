<?php namespace Culpa\Facades;

use Culpa\Database\Schema\Blueprint;
use Culpa\Database\Schema\Builder;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Culpa\Database\Schema\Builder
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string $name
     * @return \Illuminate\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return self::getSchemaBuilder(static::$app['db']->connection($name));
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected static function getFacadeAccessor()
    {
        return self::getSchemaBuilder(static::$app['db']->connection());
    }

    /**
     * Retrieve the schema builder for the database connection. And set a custom blueprint resolver to return an
     * instance of the Culpa Blueprint class.
     *
     * @param Connection $connection
     * @return \Illuminate\Database\Schema\Builder
     */
    protected static function getSchemaBuilder(Connection $connection)
    {
        $schemaBuilder = $connection->getSchemaBuilder();
        $schemaBuilder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });

        return $schemaBuilder;
    }

}