<?php
/**
 * Blameable auditing support for Laravel's Eloquent ORM
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */

namespace Culpa;

use Illuminate\Support\ServiceProvider;

class CulpaServiceProvider extends ServiceProvider
{

    private $configLocation;

    /**
     * @inheritdoc
     * @param \Illuminate\Contracts\Foundation\Application $application
     */
    public function __construct($application)
    {
        $this->configLocation = __DIR__ . '/../config/culpa.php';
        parent::__construct($application);
    }

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->configLocation => config_path('culpa.php')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configLocation, 'culpa');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('culpa');
    }

}
