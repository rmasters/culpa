<?php

namespace Culpa;

use Illuminate\Database\Eloquent\Model;

class CustomBlameableTest extends \CulpaTest
{
    private $model;

    public function setUp()
    {
        if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('This test uses a model that uses traits.');
        }

        require_once __DIR__ . '/Models/CustomFieldsModel.php';

        parent::setUp();
        $this->model = new CustomBlameableModel;
    }

    public function testBlameables()
    {
        $this->assertTrue($this->model->isBlameable('created'));
        $this->assertTrue($this->model->isBlameable('updated'));
        $this->assertTrue($this->model->isBlameable('deleted'));
    }
}
