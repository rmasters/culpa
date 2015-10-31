<?php

namespace Culpa\Tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\FullyBlameableModel;
use Culpa\Tests\Models\SwitchableBlameableModel;
use Illuminate\Support\Facades\Auth;

class DisableBlameableTest extends CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new SwitchableBlameableModel();
    }


    public function testModelCanControlBlameableBehavior()
    {
        $this->assertTrue($this->model->isBlameable('created'));
        $this->assertTrue($this->model->isBlameable('updated'));
        $this->assertTrue($this->model->isBlameable('deleted'));

        $this->model->title = 'Hello, world!';
        $this->assertTrue($this->model->saveWithoutBlameable());

        $this->model = SwitchableBlameableModel::find($this->model->id);
        $this->assertNotNull($this->model->created_at);
        $this->assertNotNull($this->model->updated_at);
        $this->assertNull($this->model->created_by);
        $this->assertNull($this->model->updated_by);
        $this->assertNull($this->model->deleted_by);

        $this->model->title = 'Goodbye, test!';
        $this->model->save();
        $this->assertNull($this->model->created_by);
        $this->assertNotNull($this->model->updated_by);
        $this->assertNull($this->model->deleted_by);
    }
}
