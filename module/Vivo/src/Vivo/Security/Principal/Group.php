<?php
namespace Vivo\Security\Principal;

/**
 * Group
 */
class Group implements GroupInterface
{
    /**
     * Security domain
     * @var string
     */
    protected $domain;

    /**
     * Name of the group
     * @var string
     */
    protected $groupName;

    /**
     * Sets the name of the security domain
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns name of the security domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the name of the security group
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * Returns name of the security group
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Returns security principal name
     * @return string
     */
    public function getName()
    {
        $name   = $this->getDomain() . '\\' . $this->getGroupName();
        return $name;
    }
}