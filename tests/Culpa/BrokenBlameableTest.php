<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with deletedBy as well
 */
class BrokenBlameableModel extends Model
{
    use Blameable;
    protected $table = 'posts';
    protected $softDelete = true;

    protected $blameable = 42;
}

class BrokenBlameableTest extends \CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new BrokenBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertFalse($this->model->isBlameable('created'));
        $this->assertFalse($this->model->isBlameable('updated'));
        $this->assertFalse($this->model->isBlameable('deleted'));
    }
}
