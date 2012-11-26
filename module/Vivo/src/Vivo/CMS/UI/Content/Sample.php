<?php
namespace Vivo\CMS\UI\Content;

use Vivo\UI\ComponentContainerInterface;
use Zend\Http\Response;
use Vivo\CMS\UI\Component;

class Sample extends Component
{

    private $response;

    public function __construct(Response $response, \Vivo\UI\Page $page,
        $options)
    {
        $this->response = $response;
        //$this->response->setStatusCode(302);
    }

    public function init()
    {
//        echo $this->getPath();
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function submit($arg1, $arg2) {
        $this->view->call = __METHOD__ . ">>> arg1:$arg1 arg2:$arg2";
    }
}