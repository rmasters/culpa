<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Observer;

use Illuminate\Support\Facades\Config;

class BlameableObserver
{
    /** @var array $fields Mapping of events to fields */
    private $fields;

    // Default field names for states
    protected static $defaultFields = array(
        'created' => 'created_by',
        'updated' => 'updated_by',
        'deleted' => 'deleted_by',
    );

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
            if ($this->isBlameable($model, 'created') && !$model->exists && !$model->isDirty($this->getColumn($model, 'created'))) {
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
     * @param int                                 $user
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
     * @param int                                 $user
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
     * @param int                                 $user
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
     * values are taken as the column names.
     *
     * If values exist for any of [created, updated, deleted], the default
     * column names are used ($defaultFields in the method below).
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

            // Check if options were passed for blameable
            if ($reflectedModel->hasProperty('blameable')) {
                // Get the protected $blamable property
                $blameableProp = $reflectedModel->getProperty('blameable');
                $blameableProp->setAccessible(true);

                $blameable = $blameableProp->getValue($model);
            } else {
                // Model doesn't have a property for $blameable
                return array();
            }
        }

        $fields = array();
        if (is_array($blameable)) {
            // Created
            if (array_key_exists('created', $blameable)) {
                // Custom field name given
                $fields['created'] = $blameable['created'];
            } elseif (in_array('created', $blameable)) {
                //  Use the default field name
                $fields['created'] = self::$defaultFields['created'];
            }

            // Updated
            if (array_key_exists('updated', $blameable)) {
                // Custom field name given
                $fields['updated'] = $blameable['updated'];
            } elseif (in_array('updated', $blameable)) {
                //  Use the default field name
                $fields['updated'] = self::$defaultFields['updated'];
            }

            // Deleted
            if (array_key_exists('deleted', $blameable)) {
                // Custom field name given
                $fields['deleted'] = $blameable['deleted'];
            } elseif (in_array('deleted', $blameable)) {
                //  Use the default field name
                $fields['deleted'] = self::$defaultFields['deleted'];
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
