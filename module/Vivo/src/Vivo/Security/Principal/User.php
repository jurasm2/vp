<?php
namespace Vivo\Security\Principal;

use DateTime;

/**
 * User
 * User object
 */
class User implements UserInterface
{
    /**
     * Username
     * @var string
     */
    protected $username;

    /**
     * User's full name
     * @var string
     */
    protected $fullName;

    /**
     * Security domain
     * @var string
     */
    protected $domain;

    /**
     * Password hash
     * @var string
     */
    protected $passwordHash;

    /**
     * User's e-mail
     * @var string
     */
    protected $email;

    /**
     * Is user account active?
     * @var bool
     */
    protected $active   = false;

    /**
     * Date of account expiration or null for no expiration
     * @var DateTime
     */
    protected $expiration;

    /**
     * Sets username
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns username
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns security principal name
     * @return string
     */
    public function getName()
    {
        $name   = $this->getDomain() . '\\' . $this->getUsername();
        return $name;
    }

    /**
     * Sets security domain
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns security domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets user's full name
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Returns user's full name
     * @return string
     */
    public function getFullName()
    {
        if ($this->fullName) {
            $fullName   = $this->fullName;
        } else {
            $fullName   = $this->getName();
        }
        return $fullName;
    }

    /**
     * Sets password hash
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Returns password hash
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Sets e-mail
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns user's e-mail
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets if the user account is active
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    /**
     * Is the user active
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Sets the account expiration (null = no expiration)
     * @param \DateTime $expiration
     */
    public function setExpiration(DateTime $expiration = null)
    {
        $this->expiration = $expiration;
    }

    /**
     * Returns expiration of the user account or null if the account does not expire
     * @return DateTime
     */
    public function getExpiration()
    {
        return $this->expiration;
    }
}