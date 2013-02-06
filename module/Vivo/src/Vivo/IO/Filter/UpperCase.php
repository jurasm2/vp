<?php
namespace Vivo\IO\Filter;

use Vivo\IO\InputStreamInterface;

/**
 * IO filter that convert stream to upper case.
 *
 */
class UpperCase implements InputStreamInterface
{

    /**
     * @var InputStreamInterface
     */
    protected $is;

    /**
     * Constructor.
     * @param InputStreamInterface $inputStream
     */
    public function __construct(InputStreamInterface $inputStream)
    {
        $this->is = $inputStream;
    }

    /**
     * Reads data from inputstream and converts it to uppser case.
     *
     * @see \Vivo\IO\InputStreamInterface::read()
     */
    public function read($bytes = 1)
    {
        $data = $this->is->read($bytes);
        if ($data === false) {
            return false;
        }
        return strtoupper($data);
    }
}
