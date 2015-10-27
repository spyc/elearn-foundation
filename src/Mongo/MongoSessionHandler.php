<?php
/**
 * HHVM
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Mongo;

use Jenssegers\Mongodb\Connection;
use SessionHandlerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;

class MongoSessionHandler extends MongoDbSessionHandler implements SessionHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $collection;

    /**
     * @param Connection $connection
     * @param string $collection
     */
    public function __construct(Connection $connection, $collection)
    {
        $database = $connection->getMongoDB();
        $options = [
            'database' => $database,
            'collection' => $collection,
            'id_field' => '_id',
            'data_field' => 'payload',
            'time_field' => 'last_activity'
        ];
        parent::__construct($connection->getMongoClient(), $options);
    }
}