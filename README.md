# Blameable hooks for Laravel Eloquent models

This extension automatically adds references to the authenticated user when
creating, updating or soft-deleting a model.

[![master](https://travis-ci.org/rmasters/culpa.png?branch=master)](https://travis-ci.org/rmasters/culpa)


## Installation

This implementation requires PHP 5.4 as it uses traits to extend the model.

1.  Add to the require section of your `composer.json`:
    `"rmasters/culpa": "dev-master"` and `composer update`,
2.  Add to the `providers` list in config/app.php:
    `"Culpa\CulpaServiceProvider"`.


## Usage

You can add blameable fields on a per-model basis by adding the trait
`Culpa\Blameable` to your model classes, and setting an array of events to record.

    class Comment extends Eloquent {
        use Culpa\Blameable;

        protected $blameables = ['created', 'updated', 'deleted'];
        
*   On create, the authed user will be set in `created_by_id`,
*   On update, the authed user will be set in `updated_by_id`,
*   Additionally, if the model is soft-deletable, the authed user will be set in
    `deleted_by_id`.

The names of the columns used can be changed by modifying the keys:

    protected $blameables = ['created' => 'author_id', 'updated' => 'revised_by_id'];

Finally, you will need to add these fields to your migrations.

The `createdBy`, `updatedBy` & `deletedBy` fields `belongsTo()` the model
defined by `$blameableModel`, which is "User" by default. You can change the
class by defining the `$blameableModel` property on your model, and potentially
remove the relationships by overriding those methods (I'm not sure PHP's trait
system works that way, to be looked into).


## License

Culpa is released under the [MIT License](LICENSE).

