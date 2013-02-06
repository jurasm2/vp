<?php
namespace Vivo\Security\Principal;

/**
 * GroupInterface
 */
interface GroupInterface extends PrincipalInterface
{
    /**
     * Returns name of the security domain
     * @return string
     */
    public function getDomain();

    /**
     * Returns name of the security group
     * @return string
     */
    public function getGroupName();
}