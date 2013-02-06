<?php
namespace Vivo\Security\Principal;

interface PrincipalInterface
{
    /**
     * Returns security principal name
     * @return string
     */
    public function getName();
}