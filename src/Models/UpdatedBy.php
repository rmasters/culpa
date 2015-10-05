<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Model;

use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them.
 *
 * @property \Illuminate\Database\Eloquent\Model $updated_by The updater of this model
 */
trait UpdatedBy
{
    /**
     * Get the user that updated the model.
     *
     * @return \Illuminate\Database\Eloquent\Model User instance
     */
    public function updatedBy()
    {
        $model = Config::get('culpa.users.classname', 'App\User');

        return $this->belongsTo($model)->withTrashed();
    }
}
