<?php

namespace Culpa;

class BrokenBlameableTest extends \CulpaTest
{
    private $model;

    public function setUp()
    {
        if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $this->markTestSkipped('This test uses a model that uses traits.');
        }

        require_once __DIR__ . '/Models/BrokenModel.php';

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
