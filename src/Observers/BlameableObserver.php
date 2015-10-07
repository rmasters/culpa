<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Observers;

use Illuminate\Support\Facades\Config;

class BlameableObserver
{
    /** @var array $fields Mapping of events to fields */
    private $fields;

    /**
     * Creating event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function creating($model)
    {
        $this->updateBlameables($model);
    }

    /**
     * Updating event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function updating($model)
    {
        $this->updateBlameables($model);
    }

    /**
     * Deleting event.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function deleting($model)
    {
        $this->updateDeleteBlameable($model);
    }

    /**
     * Update the blameable fields.
     */
    protected function updateBlameables($model)
    {
        $user = $this->activeUser();

        if ($user) {
            // Set updated-by if it has not been touched on this model
            if ($this->isBlameable($model, 'updated') && !$model->isDirty($this->getColumn($model, 'updated'))) {
                $this->setUpdatedBy($model, $user);
            }

            // Set created-by if the model does not exist
            if (
                $this->isBlameable($model, 'created') && !$model->exists
                && !$model->isDirty($this->getColumn($model, 'created'))
            ) {
                $this->setCreatedBy($model, $user);
            }
        }
    }

    /**
     * Update the deletedBy blameable field.
     */
    public function updateDeleteBlameable($model)
    {
        $user = $this->activeUser();

        if ($user) {
            // Set deleted-at if it has not been touched
            if ($this->isBlameable($model, 'deleted') && !$model->isDirty($this->getColumn($model, 'deleted'))) {
                $this->setDeletedBy($model, $user);
                $model->save();
            }
        }
    }

    /**
     * Get the active user.
     *
     * @return int User ID
     * @throws \Exception
     */
    protected function activeUser()
    {
        $fn = Config::get('culpa.users.active_user');
        if (!is_callable($fn)) {
            throw new \Exception('culpa.users.active_user should be a closure');
        }

        return $fn();
    }

    /**
     * Set the created-by field of the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int $user
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function setCreatedBy($model, $user)
    {
        $model->{$this->getColumn($model, 'created')} = $user;

        return $model;
    }

    /**
     * Set the updated-by field of the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int $user
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function setUpdatedBy($model, $user)
    {
        $model->{$this->getColumn($model, 'updated')} = $user;

        return $model;
    }

    /**
     * Set the deleted-by field of the model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int $user
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function setDeletedBy($model, $user)
    {
        $model->{$this->getColumn($model, 'deleted')} = $user;

        return $model;
    }

    /**
     * Get the created/updated/deleted-by column, or null if it is not used.
     *
     * @param string $event One of (created|updated|deleted)
     *
     * @return string|null
     */
    public function getColumn($model, $event)
    {
        if (array_key_exists($event, $this->getBlameableFields($model))) {
            $fields = $this->getBlameableFields($model);

            return $fields[$event];
        } else {
            return;
        }
    }

    /**
     * Does the model use blameable fields for an event?
     *
     * @param string $event One of (created|updated|deleted), or omitted for any
     *
     * @return bool
     */
    public function isBlameable($model, $event = null)
    {
        return $event ?
            array_key_exists($event, $this->getBlameableFields($model)) :
            count($this->getBlameableFields($model)) > 0;
    }

    /**
     * Evaluate the blameable fields to use, using reflection to find a protected $blameable property.
     *
     * If keys in $blameable exist for any of [created, updated, deleted], the
     * values are taken as the column names
     *
     * Examples:
     *   private $blameable = ['created', 'updated'];
     *   private $blameable = ['created' => 'author_id'];
     *   private $blameable = ['created', 'updated', 'deleted' => 'killedBy'];
     *
     * @param array|null $fields Optionally, the $blameable array can be given rather than using reflection
     *
     * @return array
     */
    public static function findBlameableFields($model, $blameable = null)
    {
        if (is_null($blameable)) {
            // Get the reflected model instance in order to access $blameable
            $reflectedModel = new \ReflectionClass($model);
            if (!$reflectedModel->hasProperty('blameable')) {
                return array();
            }

            $blameableProp = $reflectedModel->getProperty('blameable');
            $blameableProp->setAccessible(true);
            $blameable = $blameableProp->getValue($model);
        }

        if (is_array($blameable)) {
            return self::extractBlamableFields($blameable);
        }

        return array();
    }

    /**
     * Internal method that matches the extracted blamable property values with eloquent fields
     *
     * @param array $blameableValue
     *
     * @return array
     */
    protected static function extractBlamableFields(array $blameableValue)
    {
        $fields = array();
        $checkedFields = array('created', 'updated', 'deleted');

        foreach ($checkedFields as $possibleField) {
            if (array_key_exists($possibleField, $blameableValue)) {
                $fields[$possibleField] = $blameableValue[$possibleField];
                continue;
            }

            if (in_array($possibleField, $blameableValue)) {
                $defaultValue = $possibleField . '_by';
                $configKey = 'culpa.default_fields.' . $possibleField;
                $fields[$possibleField] = Config::get($configKey, $defaultValue);
            }
        }

        return $fields;
    }

    /**
     * Get the blameable fields.
     *
     * @return array
     */
    protected function getBlameableFields($model)
    {
        if (isset($this->fields)) {
            return $this->fields;
        }

        $this->fields = self::findBlameableFields($model);

        return $this->fields;
    }
}
