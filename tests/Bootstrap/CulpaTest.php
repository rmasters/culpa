<?php

namespace Culpa\tests\Bootstrap;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit_Framework_TestCase;

class CulpaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Support\Container Inversion-of-Control container
     */
    public static $app;

    public static function setUpBeforeClass()
    {
        if (!isset(self::$app)) {
            self::$app = AppFactory::create();
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->increments('id');
                $table->string('name');
            });
        }

        DB::insert('insert into users (name) values (?)', array('Test User'));

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function ($table) {
                $table->increments('id');
                $table->string('title');

                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
                $table->integer('deleted_by')->unsigned()->nullable();

                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');
                $table->foreign('deleted_by')->references('id')->on('users');
            });
        }
    }

    public static function tearDownAfterClass()
    {
        Schema::drop('users');
        Schema::drop('posts');

        Mockery::close();
    }
}
