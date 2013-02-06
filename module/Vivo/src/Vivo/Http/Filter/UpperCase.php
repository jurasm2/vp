<?php
namespace Vivo\Http\Filter;

use Vivo\Http\StreamResponse;
use Vivo\IO\Filter;
use Zend\Http\Request;

/**
 * Upper case output filter.
 *
 * @example This class shows how to use the output filters.
 */

class UpperCase implements OutputFilterInterface
{
    /**
     * Attach UpperCase filter.
     *
     * Filter is attached only for text/html contenttype.
     *
     * @see \Vivo\Http\Filter\OutputFilterInterface::attachFilter()
     */
    public function attachFilter(Request $request, StreamResponse $response)
    {
        if ($response->getHeaders()->get('Content-Type')->getFieldValue()
                == 'text/html') {
            $response->setInputStream(
                    new Filter\UpperCase($response->getInputStream()));
            return true;
        }
        return false;
    }
}
