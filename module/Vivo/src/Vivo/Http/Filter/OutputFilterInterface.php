<?php
namespace Vivo\Http\Filter;

use Vivo\Http\StreamResponse;
use Zend\Http\Request;

interface OutputFilterInterface {

    /**
     * Attach output filter.
     *
     * Method returns whether filter was attached or not.
     *
     * @param Request $request
     * @param StreamResponse $response
     * @return boolean whether filter was atached.
     */
    public function attachFilter(Request $request, StreamResponse $response);
}
