# Culpa [![Latest Stable Version](https://poser.pugx.org/rmasters/culpa/v/stable.png)](https://packagist.org/packages/rmasters/culpa) [![master](https://travis-ci.org/rmasters/culpa.png?branch=master)](https://travis-ci.org/rmasters/culpa)


Blameable extension for Laravel's Eloquent ORM models. This extension
automatically adds references to the authenticated user when creating, updating
or soft-deleting a model.


## Installation

This package works with Laravel 5.1 (running PHP 5.5.9+).

To install the package in your project:

1.  Add to the require section of your `composer.json`:
    `"rmasters/culpa": "dev-develop"`,
2.  Run `composer update`,
3.  Add to the `providers` list in config/app.php:
    `"Culpa\CulpaServiceProvider"`,
4.  Publish the configuration to your application:
    `php artisan vendor:publish`


## Usage

You can add auditable fields on a per-model basis by adding a protected property
and a model observer. The property `$blameable` contains events you wish to
record - at present this is restricted to created, updated and deleted - which
function the same as Laravel's timestamps.

```php

    use Culpa\Traits\Blameable;
    use Culpa\Traits\CreatedBy;
    use Culpa\Traits\DeletedBy;
    use Culpa\Traits\UpdatedBy;
    use Illuminate\Database\Eloquent\Model
    
    class Comment extends Eloquent
    {
        use Blameable, CreatedBy, UpdatedBy;
    
        protected $blameable = array('created', 'updated', 'deleted');
        
        // Rest of your model here
    }
```

*   On create, the authenticated user will be set in `created_by`,
*   On create and update, the authenticated user will be set in `updated_by`,
*   Additionally, if the model was soft-deletable, the authenticated user will be
    set in `deleted_by`.

To activate the automatic updating of these fields, you need to add the blamable trait to the model.
The names of the columns used can be changed by passing an associative array of event names to columns:

```php
    protected $blameable = array(
        'created' => 'author',
        'updated' => 'revised_by'
    );
```

### Changing the user source

The `culpa.users.active_user` config should yield a function that returns a
user id, or null if there is no user authenticated.

    'users' => [

        // The default implementation:
        'active_user' => function() {
            return Auth::check() ? Auth::user()->id : null;
        }

        // or, for Sentry2 integration:
        'active_user' => function() {
            return Sentry::check() ? Sentry::getUser()->id : null;
        }


### Changing the user class

By default, the fields will relate to `App\User` - this can be configured as so in
the package configuration file:

    'users' => array(

        // Use the Sentry2 user model
        'classname' => 'Cartalyst\Sentry\Users\Eloquent\User'

    )


## License

Culpa is released under the [MIT License](LICENSE).

