# Culpa [![Latest Stable Version](https://poser.pugx.org/rmasters/culpa/v/stable.png)](https://packagist.org/packages/rmasters/culpa) [![master](https://travis-ci.org/rmasters/culpa.png?branch=master)](https://travis-ci.org/rmasters/culpa) [![Coverage Status](https://coveralls.io/repos/rmasters/culpa/badge.png)](https://coveralls.io/r/rmasters/culpa) [![Dependency Status](https://www.versioneye.com/user/projects/51e0102690410600020001bb/badge.png)](https://www.versioneye.com/user/projects/51e0102690410600020001bb)


Blameable extension for Laravel's Eloquent ORM models. This extension
automatically adds references to the authenticated user when creating, updating
or soft-deleting a model.


## Installation

This package works with PHP 5.3 and above, but includes traits to make it easier
to use on PHP 5.4+.

To install the package in your project:

1.  Add to the require section of your `composer.json`:
    `"rmasters/culpa": "dev-master"`,
2.  Run `composer update`,
3.  Add to the `providers` list in config/app.php:
    `"Culpa\CulpaServiceProvider"`,
4.  Publish the configuration to your application:
    `artisan config:publish rmasters/culpa`


## Usage

You can add auditable fields on a per-model basis by adding a protected property
and a model observer. The property `$blameable` contains events you wish to
record - at present this is restricted to created, updated and deleted - which
function the same as Laravel's timestamps.

    class Comment extends Eloquent {

        protected $blameable = array('created', 'updated', 'deleted');

*   On create, the authenticated user will be set in `created_by_id`,
*   On create and update, the authenticated user will be set in `updated_by_id`,
*   Additionally, if the model is soft-deletable, the authenticated user will be
    set in `deleted_by_id`.

To activate the automatic updating of these fields, you need to add the model
observer to this model:

    class Comment extends Eloquent {

        // ...

    }
    Comment::observe(new Culpa\BlameableObserver);

The names of the columns used can be changed by passing an associative array of event names to columns:

    protected $blameable = array(
        'created' => 'author_id',
        'updated' => 'revised_by_id'
    );

You will need to add these fields to your migrations for the model (unsigned
integer fields with foreign keys as appropriate), and add accessors to your
model:

    class Comment extends Eloquent {

        public function createdBy() {
            return $this->belongsTo('User');
        }

   }

If you're using PHP 5.4 or above, you can take advantage of the provided traits
to add these methods automatically (`Culpa\CreatedBy`, `Culpa\UpdatedBy`,
`Culpa\DeletedBy`).


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
            return Sentry::check() ? Sentry::getUser()->id : null;
        }


### Changing the user class

By default, the fields will relate to `User` - this can be configured as so in
the package configuration file:

    'users' => array(

        // Use the Sentry2 user model
        'classname' => 'Cartalyst\Sentry\Users\Eloquent\User'

    )


## License

Culpa is released under the [MIT License](LICENSE).

