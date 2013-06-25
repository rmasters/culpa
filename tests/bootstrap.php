<?php
/**
 * Test bootstrapper to add some aliases
 */

$loader = require __DIR__ . "/../vendor/autoload.php";
//$loader->add('Eloquent', '');
//$loader->add('User', 'Illuminate\Support\Facades\Auth');

use Illuminate\Database\Eloquent\Model;

/**
 * Dummy user model
 */
class User extends Model
{
}
