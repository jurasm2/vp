<?php
namespace Vivo\CMS\Api;

use Vivo\Repository\RepositoryInterface;
use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\Uuid\GeneratorInterface as UuidGeneratorInterface;
use Vivo\CMS\Exception as CMSException;
use Vivo\CMS\Model;
use Vivo\CMS\Model\Content;
use DateTime;

/**
 * Document
 * Document API
 */
class Document implements DocumentInterface
{
    /**
     * Repository
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Path Builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * CMS API
     * @var CMS
     */
    protected $cmsApi;

    /**
     * UUID Generator
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor
     * @param CMS $cmsApi
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     * @param \Vivo\Uuid\GeneratorInterface $uuidGenerator
     * @param array $options
     */
    public function __construct(CMS $cmsApi,
                                RepositoryInterface $repository,
                                PathBuilderInterface $pathBuilder,
                                UuidGeneratorInterface $uuidGenerator,
                                array $options)
    {
        $this->cmsApi           = $cmsApi;
        $this->repository       = $repository;
        $this->pathBuilder      = $pathBuilder;
        $this->uuidGenerator    = $uuidGenerator;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Returns array of published contents of given document.
     * @param Model\Document $document
     * @return Model\Content[]
     */
    public function getPublishedContents(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');
        $contents   = array();
        usort($containers,
            function (Model\ContentContainer $a, Model\ContentContainer $b)
            {
                return $a->getOrder() > $b->getOrder();
            });
        foreach ($containers as $container) {
            if ($content = $this->getPublishedContent($container)) {
                $contents[] = $content;
            }
        }
        return $contents;
    }

    /**
     * Returns array of published content types (class names of published contents)
     * If there are no published contents, returns an empty array
     * @param \Vivo\CMS\Model\Document $document
     * @return string[]
     */
    public function getPublishedContentTypes(Model\Document $document)
    {
        $publishedContents      = $this->getPublishedContents($document);
        $publishedContentTypes  = array();
        /** @var $publishedContent Model\Content */
        foreach ($publishedContents as $publishedContent) {
            $publishedContentTypes[]    = get_class($publishedContent);
        }
        return $publishedContentTypes;
    }

    /**
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|boolean
     * @throws \Vivo\CMS\Exception\LogicException when there is more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container)
    {
        $result = array();
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        foreach ($contents as $content) {
            /* @var $content Model\Content */
            if ($content->getState() == 'PUBLISHED') {
                $result[] = $content;
            }
        }
        if (count($result) == 1) {
            return $result[0];
        } elseif (count($result) == 0) {
            return false;
        } else {
            throw new CMSException\LogicException(
                sprintf("%s: The ContentContainer '%s' contains more than one published content",
                    __METHOD__, $container->getPath()));
        }
    }

    /**
     * @param \Vivo\CMS\Model\Content $content
     */
    public function publishContent(Model\Content $content)
    {
        $document   = $this->getContentDocument($content);
        $oldContent = $this->getPublishedContent($document, $content->getIndex());
        if ($oldContent) {
            $oldContent->setState('ARCHIVED');
            $this->cmsApi->saveEntity($oldContent, false);
        }
        $content->setState('PUBLISHED');
        $this->cmsApi->saveEntity($content, true);
    }

    /**
     * @return array
     */
    public function getWorkflowStates()
    {
        return $this->options['workflow']['states'];
    }

    /**
     * Returns all principals workflow states.
     * @return array
     */
    public function getWorkflowAvailableStates()
    {
        return $this->options['workflow']['states']; //TODO: apply security
    }

    /**
     * Sets a workflow state to the content
     * @param \Vivo\CMS\Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setWorkflowState(Model\Content $content, $state)
    {
        $states = array_keys($this->getWorkflowStates());
        if (!in_array($state, $states)) {
            throw new CMSException\InvalidArgumentException(
                sprintf("%s: Unknown state '%s', available: %s", __METHOD__, $state, implode(', ', $states)));
        }
        //TODO: authorization
        if (true /* uzivatel ma pravo na change*/) {

        }
        if ($state == 'PUBLISHED') {
            $this->publishContent($content);
        } else {
            $content->setState($state);
            $this->cmsApi->saveEntity($content);
        }
    }

    /**
     * Returns document for the given content
     * @param \Vivo\CMS\Model\Content $content
     * @return \Vivo\CMS\Model\Document
     */
    public function getContentDocument(Model\Content $content)
    {
        $path = $content->getPath();
        $components = $this->pathBuilder->getStoragePathComponents($path);
        array_pop($components);
        array_pop($components);
        $docPath = $this->pathBuilder->buildStoragePath($components, true);
        $document = $this->repository->getEntity($docPath);
        if ($document instanceof Model\Document) {
            return $document;
        }
        return null;
    }

    public function addDocumentContent(Model\Document $document, Model\Content $content, $index = 0)
    {
        $path           = $document->getPath();
        $version        = count($this->getDocumentContents($document, $index));
        $components     = array($path, 'Contents.' . $index, $version);
        $contentPath    = $this->pathBuilder->buildStoragePath($components, true);
        $content->setPath($contentPath);
        $content->setState('NEW');
        $this->cmsApi->saveEntity($content);
    }

    /**
     * @param Model\Document $document
     * @return Model\ContentContainer[]
     */
    public function getContentContainers(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');

        usort($containers, function($a, $b) { /* @var $a \Vivo\CMS\Model\ContentContainer */
            return $a->getOrder() > $b->getOrder();
        });

        return $containers;
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return array
     */
    public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/)
    {
        if (!is_integer($index)) {
            throw new CMSException\InvalidArgumentException(
                sprintf(
                    'Argument %d passed to %s must be an type of integer, %s given',
                    2, __METHOD__, gettype($index)));
        }
        $pathElements   = array($document->getPath(), 'Contents.', $index);
        $path           = $this->pathBuilder->buildStoragePath($pathElements, true);
        return $this->repository->getChildren(new Model\Entity($path));
    }

    /**
     * @param Model\Document $parent
     * @param Model\Document $document
     * @throws \Vivo\CMS\Api\Exception\InvalidTitleException
     * @return \Vivo\CMS\Model\Document
     */
    public function createDocument(Model\Document $parent, Model\Document $document)
    {
        $title = trim($document->getTitle());
        if($title == '') {
            throw new \Vivo\CMS\Api\Exception\InvalidTitleException('Document title is not set');
        }
        $document->setTitle($title);

        $titleLc = mb_strtolower($document->getTitle());
        $path = $this->pathBuilder->buildStoragePath(array($parent->getPath(), $titleLc));

        $document->setPath($path);
        $document = $this->cmsApi->prepareEntityForSaving($document);

        $this->repository->saveEntity($document);
        $this->repository->commit();

        return $document;
    }

    /**
     * @param Model\Document $document
     * @return Model\Document
     */
    public function saveDocument(Model\Document $document)
    {
        $options = array(
            'published_content_types' => $this->getPublishedContentTypes($document),
        );
        $this->cmsApi->saveEntity($document, $options);

        return $document;
    }

    /**
     * @param \Vivo\CMS\Model\Document $document
     * @return \Vivo\CMS\Model\ContentContainer
     */
    public function createContentContainer(Model\Document $document)
    {
        $containers = $this->getContentContainers($document);
        $count = $id = count($containers);

        try {
            while (true) {
                $path = sprintf('%s/Contents.%d', $document->getPath(), $id++);
                $this->cmsApi->getEntity($path);
            }
        }
        catch (EntityNotFoundException $e) { }

        $order = 0;
        foreach ($containers as $c) {
            $order = max($order, $c->getOrder());
        }

        $container = new Model\ContentContainer();
        $container->setPath($path);
        $container->setContainerName(sprintf('Content %d', $count + 1));
        $container->setOrder($order + 1);

        $container = $this->cmsApi->prepareEntityForSaving($container);

        $this->repository->saveEntity($container);
        $this->repository->commit();

        return $container;
    }

    /**
     * @param Model\ContentContainer $container
     * @param Model\Content $content
     * @return Model\Content
     */
    public function createContent(Model\ContentContainer $container, Model\Content $content)
    {
        $versions   = $this->getContentVersions($container);
        $highest    = -1;
        foreach ($versions as $version) {
            $verNum = (int) $this->pathBuilder->basename($version->getPath());
            if ($verNum > $highest) {
                $highest = $verNum;
            }
        }
        $path = $this->pathBuilder->buildStoragePath(array($container->getPath(), ++$highest));
        $content->setPath($path);
        $this->updateContentStates($container, $content);
        $content = $this->cmsApi->prepareEntityForSaving($content);
        $this->repository->saveEntity($content);
        $this->repository->commit();
        return $content;
    }

    /**
     * Saves content
     * The entity is prepared before saving into repository
     *
     * @param Model\Content $content
     * @return Model\Content
     */
    public function saveContent(Model\Content $content)
    {
        $container = $this->cmsApi->getEntityParent($content);

        $this->updateContentStates($container, $content);

        $content = $this->cmsApi->prepareEntityForSaving($content);

        $this->repository->saveEntity($content);
        $this->repository->commit();

        return $content;
    }

    /**
     * @param Model\ContentContainer $container
     * @param Model\Content $content
     */
    protected function updateContentStates(Model\ContentContainer $container, Model\Content $content)
    {
        $contentVersions = $this->getContentVersions($container);

        foreach ($contentVersions as $version) { /* @var $version \Vivo\CMS\Model\Content */
            if($version->getUuid() !== $content->getUuid() && $version->getState() == 'PUBLISHED') {
                $version->setState('ARCHIVED');

                $this->cmsApi->saveEntity($version, false);
            }
        }
    }

    /**
     * Returns child documents.
     * @param Model\Folder $document
     * @return Model\Folder[]
     */
    public function getChildDocuments(Model\Folder $document)
    {
        $children   = $this->repository->getChildren($document);
        $result = array();
        foreach ($children as $child) {
            if ($child instanceof Model\Folder) {
                $result[] = $child;
            }
        }
        return $result;
    }

    /**
     * Returns number of contents the document has
     * @param \Vivo\CMS\Model\Document $document
     * @return integer
     */
    public function getContentCount(Model\Document $document)
    {
        $containers     = $this->getContentContainers($document);
        $contentCount   = count($containers);
        return $contentCount;
    }

    /**
     * @param Model\ContentContainer $container
     * @return Model\Content[]
     */
    public function getContentVersions(Model\ContentContainer $container)
    {
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        return $contents;
    }

    /**
     * Returns if the document has any child documents
     * @param \Vivo\CMS\Model\Document $document
     * @return bool
     */
    public function hasChildDocuments(Model\Document $document)
    {
        $childDocs      = $this->getChildDocuments($document);
        $hasChildDocs   = count($childDocs) > 0;
        return $hasChildDocs;
    }

    /**
     * @param Model\Document $document
     * @param \Vivo\CMS\Model\Site $site
     * @param string $targetUrl
     * @param string $targetName
     * @param string $title
     * @param boolean $createHyperlink
     * @throws \Vivo\CMS\Exception\Exception
     * @return Model\Document
     */
    public function moveDocument(Model\Document $document, Model\Site $site, $targetUrl, $targetName, $title,
                                 $createHyperlink)
    {
        //Add trailing slash
        $targetUrl  = $targetUrl . ((substr($targetUrl, -1) == '/') ? '' : '/');
        if (!$this->cmsApi->getSiteEntity($targetUrl, $site)) {
            //The location to move to does not exist
            throw new CMSException\Exception(
                sprintf("%s: Target location '%s' does not exist", __METHOD__, $targetUrl));
        }
        $targetUrl  .= $targetName . '/';
        $targetPath = $this->cmsApi->getEntityAbsolutePath($targetUrl, $site);
        if ($this->repository->hasEntity($targetPath)) {
            //There is an entity at the target path already
            throw new Exception\EntityAlreadyExistsException(
                sprintf("%s: There is an entity at the target path '%s'", __METHOD__, $targetPath));
        }
        /** @var $moved \Vivo\CMS\Model\Document */
        $docClone   = clone $document;
        $moved  = $this->repository->moveEntity($document, $targetPath);
        if (!$moved) {
            throw new CMSException\Exception(
                sprintf("%s: Move from '%s' to '%s' failed", __METHOD__, $document->getPath(), $targetPath));
        }
        $moved->setTitle($title);
        $moveChildren       = $this->repository->getChildren($moved, false, true);
        /** @var $subTreeEntities Model\Entity[] */
        $subTreeEntities    = array_merge(array($moved), $moveChildren);
        foreach ($subTreeEntities as $entity) {
            $this->cmsApi->saveEntity($entity, false);
        }
        //Hyperlink
        if ($createHyperlink) {
            //Document
            $docClone->setUuid($this->uuidGenerator->create());
            $docClone->setTitle($docClone->getTitle() . ' HYPERLINK');
            $this->cmsApi->saveEntity($docClone);
            //Content container
            $contentContainer   = $this->createContentContainer($docClone);
            $this->cmsApi->saveEntity($contentContainer);
            //Content - hyperlink
            $hyperlink  = new Content\Hyperlink();
            $hyperlink->setUuid($this->uuidGenerator->create());
            $hyperlink->setUrl($this->cmsApi->getEntityRelPath($moved));
            $hyperlink->setState('PUBLISHED');
            $this->createContent($contentContainer, $hyperlink);
            $this->cmsApi->saveEntity($hyperlink);
        }
        $this->repository->commit();
        return $moved;
    }

    /**
     * Copies document to a new location
     * @param \Vivo\CMS\Model\Document $document
     * @param \Vivo\CMS\Model\Site $site
     * @param string $targetUrl
     * @param string $targetName
     * @param string $title
     * @throws \Vivo\CMS\Exception\Exception
     * @return \Vivo\CMS\Model\Document
     */
    public function copyDocument(Model\Document $document, Model\Site $site, $targetUrl, $targetName, $title)
    {
        //TODO - check recursive operation
//        if (strpos($target, "$path/") === 0) {
//            throw new CMS\Exception(500, 'recursive_operation', array($path, $target));
//        }

        //Add trailing slash
        $targetUrl  = $targetUrl . ((substr($targetUrl, -1) == '/') ? '' : '/');
        if (!$this->cmsApi->getSiteEntity($targetUrl, $site)) {
            //The location to copy to does not exist
            throw new CMSException\Exception(
                sprintf("%s: Target location '%s' does not exist", __METHOD__, $targetUrl));
        }
        $targetUrl  .= $targetName . '/';
        $targetPath = $this->cmsApi->getEntityAbsolutePath($targetUrl, $site);
        if ($this->repository->hasEntity($targetPath)) {
            //There is an entity at the target path already
            throw new CMSException\Exception(
                sprintf("%s: There is an entity at the target path '%s'", __METHOD__, $targetPath));
        }
        /** @var $copied \Vivo\CMS\Model\Document */
        $copied = $this->repository->copyEntity($document, $targetPath);
        if (!$copied) {
            throw new CMSException\Exception(
                sprintf("%s: Copying from '%s' to '%s' failed", __METHOD__, $document->getPath(), $targetPath));
        }
        $copied->setTitle($title);
        $oldUuidRoot        = $copied->getUuid();
        $now                = new DateTime();
        $copyChildren       = $this->repository->getChildren($copied, false, true);
        /** @var $subTreeEntities Model\Entity[] */
        $subTreeEntities    = array_merge(array($copied), $copyChildren);
        foreach ($subTreeEntities as $entity) {
            $oldUuid    = $entity->getUuid();
            $newUuid    = $this->uuidGenerator->create();
            //Assign a new UUID
            $entity->setUuid($newUuid);
            //Replace old UUID refs with new ones
            //$this->replaceUuidRefs($oldUuid, $newUuid, $copy);
            $entity->setCreated($now);
            if ($entity instanceof Model\Document) {
                //Document
                $entity->setPublished($now);
                $this->saveDocument($entity);
            } elseif ($entity instanceof Model\Content) {
                //Content
                $this->setWorkflowState($entity, 'NEW');
                $this->saveContent($entity);
            } else {
                //Entity
                $this->cmsApi->saveEntity($entity);
            }
        }
        $newUuidRoot    = $copied->getUuid();
        $this->replaceUuidRefs($oldUuidRoot, $newUuidRoot, $copied);
        $this->repository->commit();
        return $copied;
    }

    /**
     * Replaces UUIDs in a subtree
     * @param string $oldUuid
     * @param string $newUuid
     * @param Model\Document $rootDoc
     */
    public function replaceUuidRefs($oldUuid, $newUuid, Model\Document $rootDoc)
    {
        $children           = $this->repository->getChildren($rootDoc, false, true);
        /** @var $subTreeEntities Model\Entity[] */
        $subTreeEntities    = array_merge(array($rootDoc), $children);
        foreach ($subTreeEntities as $entity) {
            if ($entity instanceof Model\Content\File && $entity->getMimeType() == 'text/html') {
                /** @var $entity Model\Content\File */
                /** @var $versionOriginal \Vivo\CMS\Model\Content */
                $filename           = $entity->getFilename();
                $html               = $this->repository->getResource($entity, $filename);
                $html               = str_replace("[ref:$oldUuid]", "[ref:$newUuid]", $html);
                $this->repository->saveResource($entity, $filename, $html);
            }
        }
    }

    /**
     * Returns array of all document content versions
     * @param Model\Document $document
     * @return Model\Content[]
     */
    protected function getAllContentVersions(Model\Document $document)
    {
        $allVersions        = array();
        $contentContainers  = $this->getContentContainers($document);
        foreach ($contentContainers as $contentContainer) {
            $versions       = $this->getContentVersions($contentContainer);
            $allVersions    = array_merge($allVersions, $versions);
        }
        return $allVersions;
    }

    /**
     * @param \Vivo\CMS\Model\Folder $folder
     * @return \Vivo\CMS\Model\Folder
     */
    public function getParentDocument(Model\Folder $folder)
    {
        $parent = $this->cmsApi->getParent($folder);
        return ($parent instanceof Model\Folder) ? $parent : null;
    }

    /**
     * @return array
     */
    public function getAvailableLanguages()
    {
        return $this->options['languages'];
    }
}
