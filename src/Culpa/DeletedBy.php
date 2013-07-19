<?php
/**
 * Blameable auditing support for Laravel's Eloquent ORM
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */

namespace Culpa;

use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them
 *
 * @property \Illuminate\Database\Eloquent\Model $deleted_by The deleter of this model
 * @property int $deleted_by_id User id of the model deleter
 */
trait DeletedBy
{
    /**
     * Get the user that deleted the model
     * @return \Illuminate\Database\Eloquent\Model User instance
     */
    public function deletedBy()
    {
        $model = Config::get('culpa::users.classname', 'User');
        return $this->belongsTo($model);
    }
}
