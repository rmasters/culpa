<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with deletedBy as well
 */
class NotBlameableModel extends Model
{
    use Blameable;
    protected $blameables = array();
}

class NotBlameableTest extends \PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp()
    {
        $this->model = new NotBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertFalse($this->model->isBlameable('created'));
        $this->assertFalse($this->model->isBlameable('updated'));
        $this->assertFalse($this->model->isBlameable('deleted'));
    }
}