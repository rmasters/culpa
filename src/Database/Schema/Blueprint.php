<?php namespace Culpa\Database\Schema;

use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;
use Illuminate\Support\Facades\Config;

class Blueprint extends IlluminateBlueprint
{
    /**
     * Single method to configure all blameable fields in the table
     *
     * @param array $fields
     * @see Blueprint::createdBy()
     * @see Blueprint::updatedBy()
     * @see Blueprint::deletedBy()
     * @throws \Exception
     */
    public function blameable($fields = array('created', 'updated', 'deleted'))
    {
        if (in_array('created', $fields)) {
            $this->createdBy();
        }

        if (in_array('updated', $fields)) {
            $this->updatedBy();
        }

        if (in_array('deleted', $fields)) {
            $this->deletedBy();
        }
    }

    /**
     * Add the blameable creator field
     *
     * @see Illuminate\Database\Schema\Blueprint::integer()
     * @return \Illuminate\Support\Fluent
     * @throws \Exception
     */
    public function createdBy()
    {
        $columnName = Config::get('culpa.default_fields.created');
        if (!$columnName) {
            throw new \Exception('No column for the created field is configured, did you publish the Culpa config?');
        }

        $field = $this->integer($columnName)->unsigned();
        $this->addCulpaForeign($columnName);

        return $field;
    }

    /**
     * Add the blameable updater field
     *
     * @see Illuminate\Database\Schema\Blueprint::integer()
     * @return \Illuminate\Support\Fluent
     * @throws \Exception
     */
    public function updatedBy()
    {
        $columnName = Config::get('culpa.default_fields.updated');
        if (!$columnName) {
            throw new \Exception('No column for the updated field is configured, did you publish the Culpa config?');
        }

        $field = $this->integer($columnName)->unsigned();
        $this->addCulpaForeign($columnName);

        return $field;
    }

    /**
     * Add the blameable eraser field
     *
     * @see Illuminate\Database\Schema\Blueprint::integer()
     * @return \Illuminate\Support\Fluent
     * @throws \Exception
     */
    public function deletedBy()
    {
        $columnName = Config::get('culpa.default_fields.deleted');
        if (!$columnName) {
            throw new \Exception('No column for the deleted field is configured, did you publish the Culpa config?');
        }

        $field = $this->integer($columnName)->unsigned();
        $this->addCulpaForeign($columnName);

        return $field;
    }

    /**
     * Add a foreign key constraint to the users table
     *
     * Failing to configure a users table in the configuration does not break this method, although you
     * should never neglect the foreign keys, the schema blueprint can function without them.
     * @param $columnName
     * @return void
     */
    protected function addCulpaForeign($columnName)
    {
        $foreignTable = Config::get('culpa.users.table');
        if ($foreignTable) {
            $this->foreign($columnName)->references('id')->on($foreignTable);
        }
    }
}
