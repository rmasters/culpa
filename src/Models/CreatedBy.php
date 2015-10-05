<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Models;

use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them.
 *
 * @property \Illuminate\Database\Eloquent\Model $created_by The creator of this model
 */
trait CreatedBy
{
    /**
     * Get the user that created the model.
     *
     * @return \Illuminate\Database\Eloquent\Model User instance
     */
    public function createdBy()
    {
        $model = Config::get('culpa.users.classname', 'App\User');

        return $this->belongsTo($model)->withTrashed();
    }
}
