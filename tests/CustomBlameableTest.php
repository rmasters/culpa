<?php

namespace Culpa\Tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\CustomBlameableModel;

class CustomBlameableTest extends CulpaTest
{
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new CustomBlameableModel();
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'));
        $this->assertTrue($this->model->isBlameable('updated'));
        $this->assertTrue($this->model->isBlameable('deleted'));
    }
}
