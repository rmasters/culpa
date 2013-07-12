<?php
/**
 * Test bootstrapper to add some aliases
 */

$loader = require __DIR__ . "/../vendor/autoload.php";

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\DB;

class TestConfig implements Illuminate\Config\LoaderInterface
{
    private $config;

    public function config($namespace = null) {
        if (!isset($this->config)) {
            $this->config = [
                'default' => [
                    'database' => [
                        'default' => 'sqlite',
                        'connections' => [
                            'sqlite' => [
                                'database' => ':memory:',
                                'driver' => 'sqlite',
                                'prefix' => '',
                            ],
                        ],
                    ],
                ],
                'culpa' => require __DIR__ . '/../src/config/config.php',
            ];
        }

        return $this->config[$namespace ?: 'default'];
    }

    public function load($environment, $group, $namespace = null)
    {
        if (!array_key_exists($group, $this->config($namespace))) {
            return [];
        }
        return $this->config($namespace)[$group];
    }

    public function exists($group, $namespace = null)
    {
        return array_key_exists($group, $this->config());
    }

    public function addNamespace($namespace, $hint)
    {
        var_dump($namespace, $hint);
    }

    public function getNamespaces()
    {
        return [];
    }

    public function cascadePackage($environment, $package, $group, $items)
    {
        //
    }
}

class AppFactory {
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

        $dispatcher = new \Illuminate\Events\Dispatcher($this->app);
        $this->app->singleton('events', function () use ($dispatcher) {
            return $dispatcher;
        });

        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }

    public static function create()
    {
        $instance = new self;

        // Swap the Facade app with our container (to use these mocks)
        Facade::setFacadeApplication($instance->app);

        return $instance->app;
    }

    private function getConfig()
    {
        $config = new \Illuminate\Config\Repository(new TestConfig(), 'testing');

        return $config;
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
        $user = new User([
            'id' => 1,
            'name' => 'Test User'
        ]);

        $auth = Mockery::mock('Illuminate\Auth\Guard');

        $auth->shouldReceive('check')->andReturn(true);
        $auth->shouldReceive('user')->andReturn($user);

        return $auth;
    }
}

class CulpaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Support\Container Inversion-of-Control container
     */
    protected static $app;

    public static function setUpBeforeClass()
    {
        if (!isset(self::$app)) {
            self::$app = AppFactory::create();
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function($table) {
                $table->increments('id');
                $table->string('name');
            });
        }

        DB::insert('insert into users (name) values (?)', ['Test User']);

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function($table) {
                $table->increments('id');
                $table->string('title');

                $table->integer('created_by_id')->unsigned()->nullable();
                $table->integer('updated_by_id')->unsigned()->nullable();
                $table->integer('deleted_by_id')->unsigned()->nullable();

                $table->timestamps();
                $table->datetime('deleted_at')->nullable();

                $table->foreign('created_by_id')->references('id')->on('users');
                $table->foreign('updated_by_id')->references('id')->on('users');
                $table->foreign('deleted_by_id')->references('id')->on('users');
            });
        }
    }

    public static function tearDownAfterClass()
    {
        Schema::drop('users');
        Schema::drop('posts');

        Mockery::close();
    }
}

/**
 * Dummy user model
 */
class User extends Model
{
    protected $fillable = ['id', 'name'];
}
