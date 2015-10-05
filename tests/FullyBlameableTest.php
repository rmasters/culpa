<?php

namespace Culpa\Tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\FullyBlameableModel;

class FullyBlameableTest extends CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new FullyBlameableModel();
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'), 'Created should be blameable');
        $this->assertTrue($this->model->isBlameable('updated'), 'Updated should be blameable');
        $this->assertTrue($this->model->isBlameable('deleted'), 'Deleted should be blameable');
    }

    public function testCreate()
    {
        $this->model->title = 'Hello, world!';
        $this->assertTrue($this->model->save());

        $this->model = FullyBlameableModel::find($this->model->id);

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertEquals($this->model->created_at, $this->model->updated_at);
        $this->assertNull($this->model->deleted_at);

        // Check id references are set
        $this->assertEquals(1, $this->model->created_by);
        $this->assertEquals(1, $this->model->updated_by);
        $this->assertNull(null, $this->model->deleted_by);
    }

    public function testUpdate()
    {
        $this->model->title = 'Hello, world!';
        $this->assertTrue($this->model->save());

        // Make sure updated_at > created_at by at least 1 second
        usleep(1.5 * 1000000); // 1.5 seconds

        $this->model = FullyBlameableModel::find(1);
        $this->model->title = 'Test Post, please ignore';
        $this->assertTrue($this->model->save());

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertGreaterThan($this->model->created_at, $this->model->updated_at);
        $this->assertNull($this->model->deleted_at);

        $this->assertEquals(1, $this->model->created_by);
        $this->assertEquals(1, $this->model->updated_by);
        $this->assertEquals(null, $this->model->deleted_by);
    }

    public function testDelete()
    {
        $this->model->title = 'Hello, world!';
        $this->assertTrue($this->model->save());

        $this->model = FullyBlameableModel::find(1);
        $this->assertTrue($this->model->delete());

        // Reload the model
        $this->model = FullyBlameableModel::withTrashed()->find(1);

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertGreaterThan($this->model->created_at, $this->model->updated_at);
        $this->assertNotNull($this->model->deleted_at);

        $this->assertEquals(1, $this->model->created_by);
        $this->assertEquals(1, $this->model->updated_by);
        $this->assertEquals(1, $this->model->deleted_by);
    }
}
