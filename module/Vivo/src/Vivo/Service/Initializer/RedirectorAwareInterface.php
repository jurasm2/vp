<?php
namespace Vivo\Service\Initializer;

use Vivo\Util\Redirector;

use Zend\Stdlib\RequestInterface;

/**
 * Interface for injecting Redirector
 */
interface RedirectorAwareInterface
{
    /**
     * Injects redirector
     * @param \Vivo\Util\Redirector $redirector
     * @return void
     */
    public function setRedirector(Redirector $redirector);
}
