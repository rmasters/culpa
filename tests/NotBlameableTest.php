<?php

namespace Culpa\tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\NotBlameableModel;

class NotBlameableTest extends CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new NotBlameableModel();
    }

    public function testBlameables()
    {
        $this->assertFalse($this->model->isBlameable('created'));
        $this->assertFalse($this->model->isBlameable('updated'));
        $this->assertFalse($this->model->isBlameable('deleted'));
    }
}
