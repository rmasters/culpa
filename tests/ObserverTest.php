<?php

namespace Culpa\Tests;

use Culpa\Tests\Bootstrap\CulpaTest;
use Culpa\Tests\Models\FullyBlameableModel;
use Culpa\Tests\Models\User;

/**
 * Due to protected methods called from the constructor, i was having trouble to test the actual booting of the Trait.
 * @package Culpa\Tests
 */
class ObserverTest extends CulpaTest
{
    /**
     * The Default user model does not have a listener bind to the updating and deleting model events.
     * However, when loading the blameable trait, the Observer should auto register itself for the model.
     */
    public function testObserverBind()
    {
        $model = new User(); // Booting a normal model that does not include the Blameable trait
        $this->assertFalse($model->getEventDispatcher()->hasListeners('eloquent.creating: Culpa\Tests\Models\User'));
        $this->assertFalse($model->getEventDispatcher()->hasListeners('eloquent.updating: Culpa\Tests\Models\User'));
        $this->assertFalse($model->getEventDispatcher()->hasListeners('eloquent.deleting: Culpa\Tests\Models\User'));

        $model = new FullyBlameableModel(); // We will boot the model, Laravel invokes the bootBlameable() method which hooks our observer to the events
        $this->assertTrue($model->getEventDispatcher()->hasListeners('eloquent.creating: Culpa\Tests\Models\FullyBlameableModel'));
        $this->assertTrue($model->getEventDispatcher()->hasListeners('eloquent.updating: Culpa\Tests\Models\FullyBlameableModel'));
        $this->assertTrue($model->getEventDispatcher()->hasListeners('eloquent.deleting: Culpa\Tests\Models\FullyBlameableModel'));
    }
}
