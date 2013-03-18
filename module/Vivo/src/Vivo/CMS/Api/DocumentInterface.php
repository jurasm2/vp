<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model;
use Vivo\CMS\Exception;

/**
 * DocumentInterface
 * Document API interface
 */
interface DocumentInterface
{
    /**
     * Returns array of published contents of given document.
     * @param Model\Document $document
     * @return Model\Content[]
     */
    public function getPublishedContents(Model\Document $document);

    /**
     * Returns array of published content types (class names of published contents)
     * If there are no published contents, returns an empty array
     * @param \Vivo\CMS\Model\Document $document
     * @return string[]
     */
    public function getPublishedContentTypes(Model\Document $document);

    /**
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|boolean
     * @throws Exception\LogicException when there is more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container);

    /**
     * @param Model\Content $content
     */
    public function publishContent(Model\Content $content);

    /**
     * @return array
     */
    public function getWorkflowStates();

    /**
     * @return array
     */
    public function getWorkflowAvailableStates();

    /**
     * Sets a workflow state to the content
     * @param Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setWorkflowState(Model\Content $content, $state);

    /**
     * Returns document for the given content
     * @param Model\Content $content
     * @return Model\Document
     */
    public function getContentDocument(Model\Content $content);

    public function addDocumentContent(Model\Document $document, Model\Content $content, $index = 0);

    /**
     * @param Model\Document $document
     * @return array <\Vivo\CMS\Model\ContentContainer>
     */
    public function getContentContainers(Model\Document $document);

    /**
     * @param Model\Document $document
     * @param int $index
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return array
     */
    public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/);

    /**
     * @param Model\Document $parent
     * @param Model\Document $document
     * @return \Vivo\CMS\Model\Document
     */
    public function createDocument(Model\Document $parent, Model\Document $document);

    /**
     * Copies document to a new location
     * @param \Vivo\CMS\Model\Document $document
     * @param \Vivo\CMS\Model\Site $site
     * @param string $targetUrl
     * @param string $targetName
     * @param string $title
     * @return Model\Document
     */
    public function copyDocument(Model\Document $document, Model\Site $site, $targetUrl, $targetName, $title);

    /**
     * @param Model\Document $document
     * @param \Vivo\CMS\Model\Site $site
     * @param string $targetUrl
     * @param string $targetName
     * @param string $title
     * @param boolean $createHyperlink
     * @return Model\Document
     */
    public function moveDocument(Model\Document $document, Model\Site $site, $targetUrl, $targetName, $title,
                                 $createHyperlink);

    public function saveDocument(Model\Document $document);

    /**
     * Returns child documents.
     * @param Model\Document $document
     * @return Model\Document[]
     */
    public function getChildDocuments(Model\Folder $document);

    /**
     * Returns number of contents the document has
     * @param \Vivo\CMS\Model\Document $document
     * @return integer
     */
    public function getContentCount(Model\Document $document);

    /**
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContentVersions(Model\ContentContainer $container);

    /**
     * Returns if the document has any child documents
     * @param \Vivo\CMS\Model\Document $document
     * @return boolean
     */
    public function hasChildDocuments(Model\Document $document);

    /**
     * @return array
     */
    public function getAvailableLanguages();
}
