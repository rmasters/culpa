<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with deletedBy as well
 */
class CustomBlameableModel extends Model
{
    use Blameable;
    protected $softDelete = true;

    protected $blameable = array(
        'created' => 'authorId',
        'updated' => 'editorId'
    );
}

class CustomBlameableTest extends \PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp()
    {
        $this->model = new CustomBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'));
        $this->assertTrue($this->model->isBlameable('updated'));
        $this->assertFalse($this->model->isBlameable('deleted'));
    }
}