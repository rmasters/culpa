<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with all 3 fields, with the default values
 */
class FullyBlameableModel extends Model
{
    use Blameable;
    protected $table = 'posts';
    protected $softDelete = true;
    protected $blameable = array('created', 'updated', 'deleted');
}
FullyBlameableModel::observe(new BlameableObserver);
