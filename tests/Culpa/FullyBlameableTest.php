<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with deletedBy as well
 */
class FullyBlameableModel extends Model
{
    use Blameable;
    protected $softDelete = true;
    protected $blameable = array('created', 'updated', 'deleted');
}

class FullyBlameableTest extends \PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp()
    {
        $this->model = new FullyBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'), "Created should be blameable");
        $this->assertTrue($this->model->isBlameable('updated'), "Updated should be blameable");
        $this->assertTrue($this->model->isBlameable('deleted'), "Deleted should be blameable");
    }
}