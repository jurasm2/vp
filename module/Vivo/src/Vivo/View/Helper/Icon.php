<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of Icon
 *
 * @author peter.krajcar
 */
class Icon extends AbstractHelper
{
    private $iconPath = '/system/Images/icons/';
    
    /**
	 * Return icon URL.
	 * @todo Determine whether there is a resource with an icon and if not, return some default URL
	 * @param string $name	Icon name (example: Discussion, Search, plus).
	 * @param int $size		Icon size, default size is 16px.
	 * @return string Path.
	 */
	public function __invoke($name, $size = 16)
    {
		return $this->iconPath.$size.'x'.$size.'/'.$name.'.png';
	}
}
