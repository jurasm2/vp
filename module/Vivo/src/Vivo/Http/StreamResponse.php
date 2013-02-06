<?php
namespace Vivo\Http;

use Vivo\IO\ByteArrayInputStream;

use Vivo\IO\OutputStreamInterface;

use Vivo\IO\CloseableInterface;
use Vivo\IO\FileOutputStream;
use Vivo\IO\InputStreamInterface;
use Vivo\IO\IOUtil;

use Zend\Http\PhpEnvironment\Response as PHPResponse;

/**
 * Response object that supports setting stream as content.
 *
 */
class StreamResponse extends PHPResponse
{

    /**
     * @var \Vivo\IO\InputStreamInterface
     */
    private $inputStream;

    /**
     * @var \Vivo\IO\OutputStreamInterface
     */
    private $outputStream;

    /**
     * @return \Vivo\IO\InputStreamInterface
     */
    public function getInputStream() {
        if ($this->inputStream == null) {
            $this->inputStream = new ByteArrayInputStream($this->content);
        }

        return $this->inputStream;
    }

    /**
     * @param InputStreamInterface $inputStream
     */
    public function setInputStream(InputStreamInterface $inputStream) {
        $this->inputStream = $inputStream;
    }

    /**
     * @return \Vivo\IO\OutputStreamInterface
     */
    public function getOutputStream() {
        if ($this->outputStream == null) {
            $this->outputStream = new FileOutputStream('php://output');
        }
        return $this->outputStream;
    }

    /**
     * @param OutputStreamInterface $outputStream
     */
    public function setOutputStream(OutputStreamInterface $outputStream) {
        $this->outputStream = $outputStream;
    }

    /* (non-PHPdoc)
     * @see Zend\Http\PhpEnvironment.Response::sendContent()
     */
    public function sendContent()
    {
        if ($this->contentSent()) {
            return $this;
        }

        $source = $this->getInputStream();
        $target = $this->getOutputStream();
        $util = new IOUtil();
        $util->copy($source, $target);
        if ($source instanceof CloseableInterface) {
            $source->close();
        }
        if ($target instanceof CloseableInterface) {
            $target->close();
        }

        $this->contentSent = true;
        return $this;
    }
}
