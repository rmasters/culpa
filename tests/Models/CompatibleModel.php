<?php

namespace Culpa\tests\Models;

use Culpa\Observer\BlameableObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

/**
 * A model with custom names for fields.
 */
class CompatibleModel extends Model
{
    use SoftDeletes;

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
CompatibleBlameableModel::observe(new BlameableObserver());
