<?php
namespace Vivo\Util;

use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

/**
 * Util class for redirecting.
 *
 */
class Redirector
{
    protected $event;

    /**
     * Constructor
     * @param MvcEvent $event
     */
    public function __construct(MvcEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Redirect to specified url.
     * @param string $url
     * @param string $statusCode
     */
    public function redirect($url = null, $statusCode = null) {
        if (!$url) {
            $url = $this->event->getRequest()->getUri()->getPath();
        }
        /* @var $response \Zend\Http\Response */
        $response = $this->event->getResponse();
        $response->setStatusCode($statusCode ?: Response::STATUS_CODE_302);
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->sendHeaders();
        die();
    }
}