<?php
namespace Vivo\Security\Principal;

use DateTime;

/**
 * UserInterface
 */
interface UserInterface extends PrincipalInterface
{
    /**
     * Returns user's security domain
     * @return string
     */
    public function getDomain();

    /**
     * Returns user's username
     * @return string
     */
    public function getUsername();

    /**
     * Returns password hash
     * @return string
     */
    public function getPasswordHash();

    /**
     * Returns user's full name
     * @return string
     */
    public function getFullName();

    /**
     * Returns user's e-mail
     * @return string
     */
    public function getEmail();

    /**
     * Is the user active
     * @return boolean
     */
    public function isActive();

    /**
     * Returns expiration of the user account or null if the account does not expire
     * @return DateTime
     */
    public function getExpiration();
}