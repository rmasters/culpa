<?php

namespace Culpa\Tests\Models;

use Culpa\Traits\Blameable;
use Culpa\Traits\CreatedBy;
use Culpa\Traits\DeletedBy;
use Culpa\Traits\UpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A model with all 3 fields and a internal method that disables the blameable fields
 */
class SwitchableBlameableModel extends Model
{
    use CreatedBy, UpdatedBy, DeletedBy, Blameable, SoftDeletes;
    protected $table = 'posts';
    protected $blameable = array('created', 'updated', 'deleted');

    public function saveWithoutBlameable()
    {
        // turn of the blameable field, assuring the field will not be updated
        $originalValues = $this->blameable;
        $this->blameable = false;

        $saved = $this->save();

        $this->blameable = $originalValues;

        return $saved;
    }
}
