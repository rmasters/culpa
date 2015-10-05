<?php

namespace Culpa\Tests\Bootstrap;

use Culpa\Tests\Models\User;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Facade;
use Mockery;

class AppFactory
{
    protected $app;

    protected function __construct()
    {
        $this->app = new Container();

        // Register mock instances with IoC container
        $config = $this->getConfig();
        $this->app->singleton('config', function () use ($config) {
            return $config;
        });

        list($connector, $manager) = $this->getDatabase();
        $this->app->singleton('db.factory', function () use ($connector) {
            return $connector;
        });
        $this->app->singleton('db', function () use ($manager) {
            return $manager;
        });

        $auth = $this->getAuth();
        $this->app->singleton('auth', function () use ($auth) {
            return $auth;
        });

        $dispatcher = new Dispatcher($this->app);
        $this->app->singleton('events', function () use ($dispatcher) {
            return $dispatcher;
        });

        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }

    public static function create()
    {
        $instance = new self();

        // Swap the Facade app with our container (to use these mocks)
        Facade::setFacadeApplication($instance->app);

        return $instance->app;
    }

    private function getConfig()
    {
        $applicationConfig = include __DIR__.'/AppConfig.php';
        $packageConfig = include __DIR__.'/../../config/culpa.php';

        $packageConfig['users']['classname'] = User::class;
        $applicationRepository = new Repository($applicationConfig);
        $applicationRepository->set('culpa', $packageConfig);

        return $applicationRepository;
    }

    private function getDatabase()
    {
        $conn = new ConnectionFactory($this->app);

        $manager = new DatabaseManager($this->app, $conn);
        $manager->setDefaultConnection('sqlite');

        return array($conn, $manager);
    }

    private function getAuth()
    {
        $user = new User(array(
            'id' => 1,
            'name' => 'Test User',
        ));

        $auth = Mockery::mock('Illuminate\Auth\Guard');

        $auth->shouldReceive('check')->andReturn(true);
        $auth->shouldReceive('user')->andReturn($user);

        return $auth;
    }
}
