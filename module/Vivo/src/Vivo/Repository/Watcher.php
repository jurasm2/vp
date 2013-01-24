<?php
namespace Vivo\Repository;

use Vivo\CMS\Model\Entity;
use Vivo\Repository\Exception;

/**
 * Watcher
 * Identity Map implementation for the Repository
 */
class Watcher
{
    /**
     * Array of all entities added so far
     * Keys are entity paths
     * @var Entity[]
     */
    protected $entities		    = array();

    /**
     * Array map uuid => path
     * @var string[]
     */
    protected $uuidToPathMap    = array();

    /* ********************************** METHODS *********************************** */

    /**
     * Adds an entity to the object watcher
     * @param \Vivo\CMS\Model\Entity $entity
     * @throws Exception\Exception
     */
    public function add(Entity $entity) {
        $uuid	= $entity->getUuid();
        if (!$uuid) {
            throw new Exception\Exception(sprintf('%s: The entity (%s) has no UUID', __METHOD__, get_class($entity)));
        }
        $path   = $entity->getPath();
        if (!$path) {
            throw new Exception\Exception(sprintf('%s: The entity (%s) has no path', __METHOD__, get_class($entity)));
        }
        $this->entities[$path]      = $entity;
        $this->uuidToPathMap[$uuid] = $path;
    }

    /**
     * Removes entity from the watcher by UUID
     * @param string $uuid
     */
    public function removeByUuid($uuid) {
        if(array_key_exists($uuid, $this->uuidToPathMap)) {
            $path   = $this->uuidToPathMap[$uuid];
            $this->removeByPath($path);
        }
    }

    /**
     * Removes entity from the watcher by path
     * @param string $path
     */
    public function removeByPath($path) {
        $uuid   = array_search($path, $this->uuidToPathMap);
        if ($uuid !== false) {
            unset($this->uuidToPathMap[$uuid]);
        }
        if (array_key_exists($path, $this->entities)) {
            unset($this->entities[$path]);
        }
    }

    /**
     * Returns entity by UUID if it already exists in the watcher, otherwise returns null
     * @param string $uuid
     * @return null|\Vivo\CMS\Model\Entity
     */
    public function getByUuid($uuid) {
        $entity	    = null;
        if (array_key_exists($uuid, $this->uuidToPathMap)) {
            $path   = $this->uuidToPathMap[$uuid];
            $entity = $this->getByPath($path);
        }
        return $entity;
    }

    /**
     * Returns entity by path if it already exists in the watcher, otherwise returns null
     * @param string $path
     * @return null|\Vivo\CMS\Model\Entity
     */
    public function getByPath($path) {
        $entity	    = null;
        if(array_key_exists($path, $this->entities)) {
            $entity = $this->entities[$path];
        }
        return $entity;
    }
}
