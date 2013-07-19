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
     * Get the model that is referred to by the blameable fields
     * @return string User model class
     */
    private function getBlameableModel()
    {
        return Config::get('culpa::users.classname', 'User');
    }

    /**
     * Get the user that created the model
     * @return \Illuminate\Database\Eloquent\Model|null User instance
     */
    public function createdBy()
    {
        if ($this->isBlameable('created')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Get the user that updated the model
     * @return \Illuminate\Database\Eloquent\Model|null User instance
     */
    public function updatedBy()
    {
        if ($this->isBlameable('updated')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Get the user that deleted the model
     * @return \Illuminate\Database\Eloquent\Model|null User instance
     */
    public function deletedBy()
    {
        if ($this->isBlameable('deleted')) {
            return $this->belongsTo($this->getBlameableModel());
        }
    }

    /**
     * Does the model use blameable fields for an event?
     *
     * @param string $event One of (created|updated|deleted), or omitted for any
     * @return bool
     */
    public function isBlameable($event = null)
    {
        return $event ? array_key_exists($event, $this->getFields()) : count($this->getFields()) > 0;
    }

    /**
     * Get blameable fields
     * @return array
     */
    protected function getFields()
    {
        if (!isset($this->fields)) {
            $this->fields = BlameableObserver::findBlameableFields($this, $this->blameable);
        }
        return $this->fields;
    }
}
