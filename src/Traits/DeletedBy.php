<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Traits;

use BadMethodCallException;
use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them.
 *
 * @property \Illuminate\Database\Eloquent\Model $deleted_by The deleter of this modelr
 */
trait DeletedBy
{
    /**
     * Get the user that deleted the model.
     *
     * @return \Illuminate\Database\Eloquent\Model User instance
     * @throws BadMethodCallException
     */
    public function eraser()
    {
        if (!method_exists($this, 'getFields')) {
            throw new BadMethodCallException('You are missing the Blameable Trait');
        }

        $fields = $this->getFields();
        $model = Config::get('culpa.users.classname', 'App\User');
        return $this->belongsTo($model, $fields['deleted']);
    }
}
