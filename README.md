# Blameable hooks for Laravel Eloquent models

This extension automatically adds references to the authenticated user when
creating, updating or soft-deleting a model.

[![master](https://travis-ci.org/rmasters/culpa.png?branch=master)](https://travis-ci.org/rmasters/culpa)


## Installation

This implementation requires PHP 5.4 as it uses traits to extend the model.

1.  Add to the require section of your `composer.json`:
    `"rmasters/laravel-blameable": "dev-master"` and `composer update`,
2.  Add to the `providers` list in config/app.php:
    `"rmasters\LaravelBlameable\LaravelBlameableServiceProvider"`.


## Usage

You can add blameable fields on a per-model basis by adding the trait
`rmasters\LaravelBlameable\BlameableTrait` to your model classes. By default:

    class Comment extends Eloquent {
        use rmasters\LaravelBlameable\BlameableTrait;
        
*   On create, the authed user will be set in `created_by_id`,
*   On update, the authed user will be set in `updated_by_id`,
*   Additionally, if the model is soft-deletable, the authed user will be set in
    `deleted_by_id`.

You can override this by specifying which of these fields to use:

    class Comment extends Eloquent {
        use rmasters\LaravelBlameable\BlameableTrait;
        
        protected $blameable = ['created', 'updated']; // Only created + updated

Or change the names of the columns used:

    protected $blameable = ['created' => 'author', 'updated' => 'revised'];

Finally, you will need to add these fields to your migrations.

The `createdBy`, `updatedBy` & `deletedBy` fields `belongsTo()` the model
defined by `$blameableModel`, which is "User" by default. You can change the
class by defining the `$blameableModel` property on your model, and potentially
remove the relationships by overriding those methods (I'm not sure PHP's trait
system works that way, to be looked into).


## License

LaravelBlameable is released under the [MIT License](LICENSE).

