<?php namespace Culpa\Database\Schema;

use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

class Blueprint extends IlluminateBlueprint
{
    public function createdBy()
    {
        die('hoi vanuit de created by functie');
    }

    public function updatedBy()
    {
        die('hoi vanuit de updated by functie');
    }

    public function deletedBy()
    {
        die('hoi vanuit de delted by functie');
    }

    public function blameable($fields = array('created', 'updated', 'deleted'))
    {
        die('hoi vanuit de blameable functie');
    }
}