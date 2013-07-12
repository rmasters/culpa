<?php
/**
 * Blameable auditing support for Laravel's Eloquent ORM
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 * @version 0.0.1
 */

namespace Culpa;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Add event-triggered references to the authorised user that triggered them
 */
trait Blameable
{
    /** @var array $fields Mapping of events to fields */
    private $fields;

    /**
     * Evaluate the blameable fields to use
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
     * @return array
     */
    private function getBlameableFields()
    {
        if (isset($this->fields)) {
            return $this->fields;
        }

        $defaultFields = array(
            'created' => 'created_by_id',
            'updated' => 'updated_by_id',
            'deleted' => 'deleted_by_id',
        );

        // Check if options were passed for blameable
        if (isset($this->blameable)) {
            if (is_array($this->blameable)) {
                $this->fields = array();

                // Created
                if (array_key_exists('created', $this->blameable)) {
                    $this->fields['created'] = $this->blameable['created'];
                } else if (in_array('created', $this->blameable)) {
                    $this->fields['created'] = $defaultFields['created'];
                }

                // Updated
                if (array_key_exists('updated', $this->blameable)) {
                    $this->fields['updated'] = $this->blameable['updated'];
                } else if (in_array('updated', $this->blameable)) {
                    $this->fields['updated'] = $defaultFields['updated'];
                }

                // Deleted
                if (array_key_exists('deleted', $this->blameable)) {
                    $this->fields['deleted'] = $this->blameable['deleted'];
                } else if (in_array('deleted', $this->blameable)) {
                    $this->fields['deleted'] = $defaultFields['deleted'];
                }
            } else {
                // Just laugh and hope they told a joke
                $this->fields = array();
            }
        } else {
            $this->fields = array();
        }

        return $this->fields;
    }

    /**
     * Get the created/updated/deleted-by column, or null if it is not used
     *
     * @param string $event One of (created|updated|deleted)
     * @return string|null
     */
    public function getColumn($event) {
        return array_key_exists($event, $this->getBlameableFields()) ?
            $this->getBlameableFields()[$event] : null;
    }

    /**
     * Does the model use blameable fields for an event?
     *
     * @param string $event One of (created|updated|deleted), or omitted for any
     * @return bool
     */
    public function isBlameable($event = null)
    {
        return $event ?
            array_key_exists($event, $this->getBlameableFields()) :
            count($this->getBlameableFields()) > 0;
    }

    /**
     * Get the active user
     *
     * @return int User ID
     */
    protected function activeUser()
    {
        $fn = Config::get('culpa::users.active_user');
        if (!is_callable($fn)) {
            throw new \Exception("culpa::users.active_user should be a closure");
        }

        return $fn();
    }

    /**
     * Update the blameable fields
     */
    public function updateBlameables()
    {
        $user = $this->activeUser();

        if ($user) {
            if (
                $this->isBlameable('updated') && 
                !$this->isDirty($this->getColumn('updated'))
            ) {
                $this->setUpdatedBy($user);
            }

            if (
                $this->isBlameable('created') &&
                !$this->exists &&
                !$this->isDirty($this->getColumn('created'))
            ) {
                $this->setCreatedBy($user);
            }
        }
    }

    /**
     * Update the deletedBy blameable field
     */
    public function updateDeleteBlameable()
    {
        $user = $this->activeUser();

        if ($user) {
            if (
                $this->isBlameable('deleted') &&
                !$this->isDirty($this->getColumn('deleted'))
            ){ 
                $this->setDeletedBy($user);
            }
        }
    }

    public function touch()
    {
        $this->updateBlameables();

        return parent::touch();
    }

    /**
     * Set the created-by relationship
     * @param int $user
     */
    public function setCreatedBy($user)
    {
        $this->{$this->getColumn('created')} = $user;
    }

    /**
     * Set the deleted-by relationship
     * @param int $user
     */
    public function setUpdatedBy($user)
    {
        $this->{$this->getColumn('updated')} = $user;
    }

    /**
     * Set the deleted-by relationship
     * @param int $user
     */
    public function setDeletedBy($user)
    {
        $this->{$this->getColumn('deleted')} = $user;
    }

    /**
     * Get the model that is referred to by the blameable fields
     * @return string User model class
     */
    private function getBlameableModel()
    {
        return Config::get('culpa::users.classname', 'User');
    }

    /**
     * Get the user that created the model
     * @return object User instance
     */
    public function createdBy()
    {
        if ($this->isBlameable('created')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Get the user that updated the model
     * @return object User instance
     */
    public function updatedBy()
    {
        if ($this->isBlameable('updated')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Get the user that deleted the model
     * @return object User instance
     */
    public function deletedBy()
    {
        if ($this->isBlameable('deleted')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Overrides the model's booter to register the event hooks
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->updateBlameables();
        });

        static::updating(function ($model) {
            $model->updateBlameables();
        });

        static::deleting(function ($model) {
            // In case this is a soft-deletable model
            // @todo Does this issue an UPDATE before the DELETE if not?
            $model->updateDeleteBlameable();
        });
    }
}
