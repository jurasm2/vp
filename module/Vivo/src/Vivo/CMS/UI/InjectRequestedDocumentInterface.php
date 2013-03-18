<?php
namespace Vivo\CMS\UI;

use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;

/**
 * Markup Interface for UI component which needs requested document
  */
interface InjectRequestedDocumentInterface
{
    /**
     * Sets requested document
     * @param Document $document
     * @return void
     */
    public function setRequestedDocument(Document $document);
}
