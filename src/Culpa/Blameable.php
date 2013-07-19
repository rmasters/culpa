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
