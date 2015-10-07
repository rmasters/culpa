<?php

namespace Culpa\Tests\Models;

use Culpa\Observers\BlameableObserver;
use Culpa\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

/**
 * A model with custom names for fields.
 */
class CompatibleBlameableModel extends Model
{
    use Blameable, SoftDeletes;

    protected $table = 'posts';

    protected $blameable = array('created', 'updated', 'deleted');

    public function createdBy()
    {
        return $this->belongsTo(Config::get('culpa.users.classname', 'App\User'));
    }

    public function updatedBy()
    {
        return $this->belongsTo(Config::get('culpa.users.classname', 'App\User'));
    }

    public function deletedBy()
    {
        return $this->belongsTo(Config::get('culpa.users.classname', 'App\User'));
    }
}
