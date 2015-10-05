<?php

namespace Culpa\Tests\Models;

use Culpa\Models\Blameable;
use Culpa\Models\CreatedBy;
use Culpa\Models\DeletedBy;
use Culpa\Models\UpdatedBy;
use Culpa\Observers\BlameableObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A model with all 3 fields, with the default values.
 */
class FullyBlameableModel extends Model
{
    use CreatedBy, UpdatedBy, DeletedBy, Blameable, SoftDeletes;
    protected $table = 'posts';
    protected $blameable = array('created', 'updated', 'deleted');
}

FullyBlameableModel::observe(new BlameableObserver());
