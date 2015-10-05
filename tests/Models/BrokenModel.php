<?php

namespace Culpa\tests\Models;

use Culpa\Observer\BlameableObserver;
use Illuminate\Database\Eloquent\Model;
use Culpa\Model\Blameable;

/**
 * A model with a silly $blameable value
 * PHP 5.4+.
 */
class BrokenModel extends Model
{
    use Blameable;
    protected $table = 'posts';
    protected $softDelete = true;

    protected $blameable = 42;
}

BrokenBlameableModel::observe(new BlameableObserver());
