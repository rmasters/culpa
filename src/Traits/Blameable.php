<?php

/**
 * Blameable auditing support for Laravel's Eloquent ORM.
 *
 * @author Ross Masters <ross@rossmasters.com>
 * @copyright Ross Masters 2013
 * @license MIT
 */
namespace Culpa\Traits;

use Culpa\Observers\BlameableObserver;

/**
 * Add event-triggered references to the authorised user that triggered them.
 */
trait Blameable
{
    /** @var array $fields Mapping of events to fields */
    private $fields;

    /**
     * Does the model use blameable fields for an event?
     *
     * @param string $event One of (created|updated|deleted), or omitted for any
     *
     * @return bool
     */
    public function isBlameable($event = null)
    {
        return $event ? array_key_exists($event, $this->getFields()) : count($this->getFields()) > 0;
    }

    /**
     * Get blameable fields.
     *
     * @return array
     */
    protected function getFields()
    {
        // Abort if blameble has been disabled explicitly
        if($this->blameable === false) return false;

        if (!isset($this->fields)) {
            $this->fields = BlameableObserver::findBlameableFields($this, $this->blameable);
        }

        return $this->fields;
    }

    /**
     * After the model is booted, Laravel will boot this Trait, allowing our observer to be bound
     * to the model.
     */
    public static function bootBlameable()
    {
        parent::observe(new BlameableObserver());
    }
}
