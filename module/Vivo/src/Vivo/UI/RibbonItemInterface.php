<?php
namespace Vivo\UI;

/**
 * Description of RibbonItemInterface
 *
 * @author peter.krajcar
 */
interface RibbonItemInterface
{
    public function isVisible();

    public function setVisible($value);
}
