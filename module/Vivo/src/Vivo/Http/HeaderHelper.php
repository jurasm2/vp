<?php
namespace Vivo\Http;

use Zend\Http\Headers;

/**
 * Helper class for modifying http headers.
 */
class HeaderHelper
{

    /**
     * Helper configuration
     * @var array
     */
    protected $config  = array();

    /**
     * Constructor.
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->config = $config;
    }

    /**
     * Add expiration http headers for specified mimeType.
     * @param Headers $headers
     * @param string $mimeType
     */
    public function setExpirationByMimeType(Headers $headers, $mimeType)
    {
        $parts = explode('/', $mimeType);
        foreach ($this->config['mime_type_expiration'] as $type => $expiration) {
            if (preg_match('/^('.$parts[0].'|\*)\/('.$parts[1].'|\*)$/', $type)) {
                break;
            }
        }
        $this->setExpiration($headers, $expiration);
    }

    /**
     * Add expiration headers using given time in seconds.
     *
     * @param Headers $headers
     * @param integer $expiration Expiration time in seconds
     */
    public function setExpiration(Headers $headers, $expiration)
    {
        $headers->addHeaderLine('Expires: '.gmdate('D, d M Y H:i:s', time() + $expiration).' GMT');
        $headers->addHeaderLine('Pragma: cache');
        $headers->addHeaderLine('Cache-Control: max-age='.$expiration);
        //$headers->addHeaderLine('Last-Modified: Thu, 02 Dec 2010 16:19:38 GMT'); //FIXME: why this line?
    }
}

