<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\UI\ComponentContainer;

/**
 * Base component for CMS UI components.
 */
class Component extends ComponentContainer implements InjectModelInterface, InjectRequestedDocumentInterface
{

    /**
     * @var Document
     */
    protected $document;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Document
     */
    protected $requestedDocument;

    /**
     * @param Document $document
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Sets requested document
     * @param Document $document
     * @return void
     */
    public function setRequestedDocument(Document $document)
    {
        $this->requestedDocument    = $document;
    }

    public function view() {
         $this->view->content           = $this->content;
         $this->view->document          = $this->document;
         $this->view->requestedDocument = $this->requestedDocument;
         return parent::view();
    }
}
