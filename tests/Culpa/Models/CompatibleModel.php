<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with custom names for fields
 */
class CompatibleBlameableModel extends Model
{
    protected $table = 'posts';
    protected $softDelete = true;

    protected $blameable = array('created', 'updated', 'deleted');
    
    public function createdBy()
    {
        return $this->belongsTo('User');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo('User');
    }
    
    public function deletedBy()
    {
        return $this->belongsTo('User');
    }
}
CompatibleBlameableModel::observe(new BlameableObserver);