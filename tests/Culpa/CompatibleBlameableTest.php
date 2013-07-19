<?php

namespace Culpa;

use Illuminate\Support\Facades\Auth;

class CompatibleBlameableTest extends \CulpaTest
{
    private $model;

    public function setUp()
    {
        require_once 'Models/CompatibleModel.php';
    
        parent::setUp();
        $this->model = new CompatibleBlameableModel;
    }
    
    public function testCreate()
    {
        $this->model->title = "Hello, world!";
        $this->assertTrue($this->model->save());

        $this->model = CompatibleBlameableModel::find($this->model->id);

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertEquals($this->model->created_at, $this->model->updated_at);
        $this->assertNull($this->model->deleted_at);

        // Check id references are set
        $this->assertEquals(1, $this->model->created_by_id);
        $this->assertEquals(1, $this->model->updated_by_id);
        $this->assertNull(null, $this->model->deleted_by_id);

        $this->assertEquals(Auth::user()->id, $this->model->created_by->id);
    }

    public function testUpdate()
    {
        $this->model->title = "Hello, world!";
        $this->assertTrue($this->model->save());
        
        // Make sure updated_at > created_at by at least 1 second
        usleep(1.5 * 1000000); // 1.5 seconds

        $this->model = CompatibleBlameableModel::find(1);
        $this->model->title = "Test Post, please ignore";
        $this->assertTrue($this->model->save());

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertGreaterThan($this->model->created_at, $this->model->updated_at);
        $this->assertNull($this->model->deleted_at);

        $this->assertEquals(1, $this->model->created_by_id);
        $this->assertEquals(1, $this->model->updated_by_id);
        $this->assertEquals(null, $this->model->deleted_by_id);

        $this->assertEquals(Auth::user()->id, $this->model->updated_by->id);
    }

    public function testDelete()
    {
        $this->model->title = "Hello, world!";
        $this->assertTrue($this->model->save());
    
        $this->model = CompatibleBlameableModel::find(1);
        $this->assertTrue($this->model->delete());

        // Reload the model
        $this->model = CompatibleBlameableModel::withTrashed()->find(1);

        // Check datetimes are being set properly for sanity's sake
        $this->assertNotNull($this->model->created_at);
        $this->assertGreaterThan($this->model->created_at, $this->model->updated_at);
        $this->assertNotNull($this->model->deleted_at);

        $this->assertEquals(1, $this->model->created_by_id);
        $this->assertEquals(1, $this->model->updated_by_id);
        $this->assertEquals(1, $this->model->deleted_by_id);

        $this->assertEquals(Auth::user()->id, $this->model->deleted_by->id);
    }
}
