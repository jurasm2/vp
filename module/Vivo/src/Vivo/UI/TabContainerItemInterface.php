<?php
namespace Vivo\UI;

/**
 * TabContainerItemInterface
 */
interface TabContainerItemInterface
{
    public function select();

    public function isDisabled();

    public function getLabel();
}
