# Culpa [![Latest Stable Version](https://poser.pugx.org/rmasters/culpa/v/stable.png)](https://packagist.org/packages/rmasters/culpa) [![master](https://travis-ci.org/rmasters/culpa.png?branch=master)](https://travis-ci.org/rmasters/culpa) [![Coverage Status](https://coveralls.io/repos/rmasters/culpa/badge.png)](https://coveralls.io/r/rmasters/culpa) [![Dependency Status](https://www.versioneye.com/user/projects/51e0102690410600020001bb/badge.png)](https://www.versioneye.com/user/projects/51e0102690410600020001bb)


Blameable extension for Laravel's Eloquent ORM models. This extension
automatically adds references to the authenticated user when creating, updating
or soft-deleting a model.


## Installation

This implementation requires PHP 5.4 as it uses traits to extend the model.

1.  Add to the require section of your `composer.json`:
    `"rmasters/culpa": "dev-master"` and `composer update`,
2.  Add to the `providers` list in config/app.php:
    `"Culpa\CulpaServiceProvider"`,
3.  Publish the configuration to your application:
    `php artisan config:publish rmasters/culpa`


## Usage

You can add blameable fields on a per-model basis by adding the trait
`Culpa\Blameable` to your model classes, and setting an array of events to record.

    class Comment extends Eloquent {
        use Culpa\Blameable;

        protected $blameable = ['created', 'updated', 'deleted'];

*   On create, the authed user will be set in `created_by_id`,
*   On update, the authed user will be set in `updated_by_id`,
   Additionally, if the model is soft-deletable, the authed user will be set in
    `deleted_by_id`.

The names of the columns used can be changed by modifying the keys:

    protected $blameable = ['created' => 'author_id', 'updated' => 'revised_by_id'];

Finally, you will need to add these fields to your migrations.

The `createdBy`, `updatedBy` & `deletedBy` fields `belongsTo()` the model
defined by `$blameableModel`, which is "User" by default. This can be configured
in the package configuration (app/config/packages/rmasters/culpa/config.php):

### Changing the user source

The `culpa::users.active_user` config should yield a function that returns a
user id, or null if there is no user authenticated.

    'users' => [

        // The default implementation:
        'active_user' => function() {
            return Auth::check() ? Auth::user()->id : null;
        }

        // or, for Sentry2 integration:
        'active_user' => function() {
            return Sentry::check() ? Sentry::user()->id : null;
        }


### Changing the user class

By default, the fields will relate to `User` - this can be configured as so:

    'users' => [

        // Use the Sentry2 user model
        'classname' => 'Cartalyst\Sentry\Users\Eloquent\User'


## License

Culpa is released under the [MIT License](LICENSE).



[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/rmasters/culpa/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

