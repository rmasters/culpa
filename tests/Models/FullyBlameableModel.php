<?php

namespace Culpa\Tests\Models;

use Culpa\Model\Blameable;
use Culpa\Model\CreatedBy;
use Culpa\Model\DeletedBy;
use Culpa\Model\UpdatedBy;
use Culpa\Observer\BlameableObserver;
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
