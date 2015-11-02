<?php
/**
 * Elearn Foundation
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/MIT MIT License
 */

namespace Elearn\Foundation\Provider;

use Illuminate\Session\EncryptedStore;
use Illuminate\Session\Store;
use Jenssegers\Mongodb\Connection;
use Elearn\Foundation\Mongo\MongoSessionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

class MongoServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }

    public function boot()
    {
        Session::extend('mongo', function (Application $app) {
            $connection = $this->app['config']['session.connection'];
            if (null === $connection) {
                $connection = $this->app['db']->connection('mongodb');
            }
            if ($connection instanceof Connection) {
                $handler = new MongoSessionHandler($connection, $app['config']['session.table']);
                if ($this->app['config']['session.encrypt']) {
                    return new EncryptedStore(
                        $this->app['config']['session.cookie'], $handler, $this->app['encrypter']
                    );
                } else {
                    return new Store($this->app['config']['session.cookie'], $handler);
                }
            }
            throw new UnexpectedTypeException(get_class($connection), Connection::class);
        });
    }
}