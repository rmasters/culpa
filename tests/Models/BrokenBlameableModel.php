<?php

namespace Culpa\Tests\Models;

use Culpa\Observers\BlameableObserver;
use Illuminate\Database\Eloquent\Model;
use Culpa\Models\Blameable;

/**
 * A model with a silly $blameable value
 * PHP 5.4+.
 */
class BrokenBlameableModel extends Model
{
    use Blameable;
    protected $table = 'posts';
    protected $softDelete = true;

    protected $blameable = 42;
}

BrokenBlameableModel::observe(new BlameableObserver());
