<?php

namespace Culpa\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = array('id', 'name');
}
