<?php

namespace Culpa\tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = array('id', 'name');
}
