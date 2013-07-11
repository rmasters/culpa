<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

/**
 * A model with custom names for fields
 */
class CustomBlameableModel extends Model
{
    use Blameable;
    protected $table = 'posts';
    protected $softDelete = true;

    protected $blameable = array(
        'created' => 'authorId',
        'updated' => 'editorId',
        'deleted' => 'purgerId',
    );
}

class CustomBlameableTest extends \CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new CustomBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'));
        $this->assertTrue($this->model->isBlameable('updated'));
        $this->assertTrue($this->model->isBlameable('deleted'));
    }

    public function testColumns()
    {
        $this->assertEquals('authorId', $this->model->getColumn('created'));
        $this->assertEquals('editorId', $this->model->getColumn('updated'));
        $this->assertEquals('purgerId', $this->model->getColumn('deleted'));
    }
}
