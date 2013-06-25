<?php

namespace Culpa;

use Illuminate\Support\Facades\Auth;

trait Blameable
{
    /**
     * Evaluate the blameable fields to use
     *
     * If keys in $blameables exist for any of [created, updated, deleted], the
     * values are taken as the column names.
     *
     * If values exist for any of [created, updated, deleted], the default
     * column names are used ($defaultFields in the method below).
     *
     * Examples:
     *   private $blameables = ['created', 'updated'];
     *   private $blameables = ['created' => 'author_id'];
     *   private $blameables = ['created', 'updated', 'deleted' => 'killedBy'];
     *
     * @return array
     */
    private function getBlameableFields()
    {
        $defaultFields = array(
            'created' => 'created_by_id',
            'updated' => 'updated_by_id',
            'deleted' => 'deleted_by_id',
        );

        // Check if options were passed for blameable
        if (isset($this->blameables)) {
            if (is_array($this->blameables)) {
                $fields = array();

                // Created
                if (array_key_exists('created', $this->blameables)) {
                    $fields['created'] = $this->blameables['created'];
                } else if (in_array('created', $this->blameables)) {
                    $fields['created'] = $defaultFields['created'];
                }

                // Updated
                if (array_key_exists('updated', $this->blameables)) {
                    $fields['updated'] = $this->blameables['updated'];
                } else if (in_array('updated', $this->blameables)) {
                    $fields['updated'] = $defaultFields['updated'];
                }

                // Deleted
                if (array_key_exists('deleted', $this->blameables)) {
                    $fields['deleted'] = $this->blameables['deleted'];
                } else if (in_array('deleted', $this->blameables)) {
                    $fields['deleted'] = $defaultFields['deleted'];
                }

                $this->blameables = $fields;
            } else {
                // Just laugh and hope they told a joke
                $this->blameables = array();
            }
        } else {
            $this->blameables = array();
        }

        return $this->blameables;
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
     * @return object User instance
     */
    protected function activeUser()
    {
        return Auth::check() ? Auth::user() : null;
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
                !$this->isDirty($this->getUpdatedByColumn())
            ) {
                $this->setUpdatedBy($user);
            }

            if (
                $this->isBlameable('created') &&
                !$this->exists &&
                !$this->isDirty($this->getCreatedByColumn())
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
                !$this->isDirty($this->getDeletedByColumn())
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

    public function setCreatedBy($user)
    {
        $this->{$this->getCreatedByColumn()} = $user;
    }

    public function setUpdatedBy($user)
    {
        $this->{$this->getUpdatedByColumn()} = $user;
    }

    public function setDeletedBy($user)
    {
        $this->{$this->getDeletedByColumn()} = $user;
    }

    private function getBlameableModel()
    {
        $exists = property_exists(get_class($this), 'blameableModel') ||
            isset($this->blameableModel);

        return $exists ? $this->blameableModel : 'User';
    }

    public function createdBy()
    {
        if ($this->isBlameable('created')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    public function updatedBy()
    {
        if ($this->isBlameable('updated')) {
            return $this->belongsTo($this->getBlameableModel());
        }

    }

    public function deletedBy()
    {
        if ($this->isBlameable('deleted')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

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
