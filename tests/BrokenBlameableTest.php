<?php

namespace Culpa\tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\BrokenBlameableModel;

class BrokenBlameableTest extends CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new BrokenBlameableModel();
    }

    public function testBlameables()
    {
        $this->assertFalse($this->model->isBlameable('created'));
        $this->assertFalse($this->model->isBlameable('updated'));
        $this->assertFalse($this->model->isBlameable('deleted'));
    }
}
