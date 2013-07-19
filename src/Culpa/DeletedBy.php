<?php
/**
 * Blameable auditing support for Laravel's Eloquent ORM
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 * @version 0.0.1
 */

namespace Culpa;

use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them
 */
trait DeletedBy
{
    /**
     * Get the user that deleted the model
     * @return \Illuminate\Database\Eloquent\Model|null User instance
     */
    public function deletedBy()
    {
        $model = Config::get('culpa::users.classname', 'User');
        return $this->belongsTo($model);
    }
}
