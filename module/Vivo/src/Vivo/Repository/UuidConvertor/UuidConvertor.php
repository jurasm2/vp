<?php
namespace Vivo\Repository\UuidConvertor;

use Vivo\Indexer\IndexerInterface;
use Vivo\Indexer\Term as IndexerTerm;
use Vivo\Indexer\Query\Term as TermQuery;
use Vivo\Indexer\Document;

/**
 * UuidConvertor
 * Converts from/to UUID to/from path
 * Caches results
 */
class UuidConvertor implements UuidConvertorInterface
{
    /**
     * Indexer instance
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * Maps UUID => path
     * Result cache
     * @var string[]
     */
    protected $uuidToPath   = array();

    /**
     * Maps path => UUID
     * Result cache
     * @var string[]
     */
    protected $pathToUuid   = array();

    /**
     * Constructor
     *
     * @param \Vivo\Indexer\IndexerInterface $indexer
     */
    public function __construct(IndexerInterface $indexer)
    {
        $this->indexer  = $indexer;
    }

    /**
     * Returns entity UUID based on its path
     * If the entity does not exist returns null instead
     * @param string $path
     * @return null|string
     */
    public function getUuid($path)
    {
        if (isset($this->pathToUuid[$path])) {
            $uuid   = $this->pathToUuid[$path];
        } else {
            $query  = new TermQuery(new IndexerTerm($path, '\path'));
            $result = $this->indexer->find($query, array('page_size' => 1));
            if ($result->getTotalHitCount() > 0) {
                $hits   = $result->getHits();
                /** @var $hit \Vivo\Indexer\QueryHit */
                $hit    = reset($hits);
                $doc    = $hit->getDocument();
                $uuid   = $doc->getFieldValue('\uuid');
                $this->set($uuid, $path);
            } else {
                $uuid   = null;
            }
        }
        return $uuid;
    }

    /**
     * Returns entity path based on its UUID
     * If the UUID does not exist returns null instead
     * @param string $uuid
     * @return null|string
     */
    public function getPath($uuid)
    {
        if (isset($this->uuidToPath[$uuid])) {
            $path   = $this->uuidToPath[$uuid];
        } else {
            $query  = new TermQuery(new IndexerTerm($uuid, '\uuid'));
            $result = $this->indexer->find($query, array('page_size' => 1));
            if ($result->getTotalHitCount() > 0) {
                $hits   = $result->getHits();
                /** @var $hit \Vivo\Indexer\QueryHit */
                $hit    = reset($hits);
                $doc    = $hit->getDocument();
                $path   = $doc->getFieldValue('\path');
                $this->set($uuid, $path);
            } else {
                $path   = null;
            }
        }
        return $path;
    }

    /**
     * Sets a conversion result (uuid and its associated path) into the result cache.
     * Overwrites previously cached results.
     *
     * @param string $uuid
     * @param string $path
     */
    public function set($uuid, $path)
    {
        $this->uuidToPath[$uuid]    = $path;
        $this->pathToUuid[$path]    = $uuid;
    }

    /**
     * Removes a conversion result (uuid and its associated path) from the result cache.
     * If $uuid is not found in cached results, does nothing.
     *
     * @param string $uuid
     */
    public function removeByUuid($uuid)
    {
        if (isset($this->uuidToPath[$uuid])) {
            $path   = $this->uuidToPath[$uuid];
            unset($this->uuidToPath[$uuid]);
            unset($this->pathToUuid[$path]);
        }
    }

    /**
     * Removes a conversion result (path and its associated uuid) from the result cache.
     * If $path is not found in cached results, does nothing.
     *
     * @param string $path
     */
    public function removeByPath($path)
    {
        if (isset($this->pathToUuid[$path])) {
            $uuid   = $this->pathToUuid[$path];
            unset($this->pathToUuid[$path]);
            unset($this->uuidToPath[$uuid]);
        }
    }
}
