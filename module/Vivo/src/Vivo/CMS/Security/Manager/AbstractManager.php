<?php
namespace Vivo\CMS\Security\Manager;

use Vivo\CMS\Model\Document;
use Vivo\CMS\Exception\Exception;
use Vivo\Security\Manager\AbstractManager as AbstractParentManager;

/**
 * AbstractManager
 * Security Manager defines base methods for working with users, roles and rights in Vivo CMS application.
 * All new Managers must extend this
 */
abstract class AbstractManager extends AbstractParentManager
{
    /* default global roles */
    const ROLE_VISITOR          = 'Visitor';
    const ROLE_PUBLISHER        = 'Publisher';
    const ROLE_ADMINISTRATOR    = 'Administrator';

    /* default user groups */
    const GROUP_MANAGERS        = 'Managers';
    const GROUP_PUBLISHERS      = 'Publishers';
    const GROUP_DEVELOPERS      = 'Developers';
    const GROUP_ADMINISTRATORS  = 'Administrators';

    /**
     * Security domain of current site.
     * @var string
     * @todo remove dependecy to site security domain
     */
    private $domain;

    /**
     * Returns current site domain.
     * @return string
     */
    public function getDomain()
    {
        return $this->domain ? : self::DOMAIN_VIVO;
    }

    /**
     * Set security domain.
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param Document $document
     * @param string $accessRight
     * @param bool $throwException
     * @param string|null $name
     * @param string|null $domain
     * @return bool
     * @throws \Vivo\CMS\Exception\Exception
     */
    public function authorize(Document $document, $accessRight, $throwException = true, $name = null, $domain = null)
    {
        //TODO - Implement this method
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));

//        $authorized = 0;
//        if (!$name) {
//            $name = $this->getPrincipalUsername();
//        }
//        if (!$domain) {
//            $domain = $this->getDomain();
//        }
//        $security = $document->security;
//        // no security definition
//        if (!$security) {
//            $authorized = 1;
//        }
//        // administrator (always authorized)
//        if (($authorized == 0) && (($name == self::USER_ADMINISTRATOR)
//                        || ($name == self::GROUP_ADMINISTRATORS)
//                        || $this->isUserInGroup($domain, $name, self::GROUP_ADMINISTRATORS))) {
//            $authorized = 1;
//        }
//        // delete by owner
//        if (($authorized == 0) && ($accessRight == 'Delete')
//                && ($name == $document->createdBy)) {
//            $authorized = 1;
//        }
//        // explicit deny
//        if ($authorized == 0) {
//            foreach ($security->deny as $deny_right => $names) {
//                if (($deny_right == $accessRight) && $this->isMemberOf($domain, $name, $names)) {
//                    $authorized = -1;
//                    break;
//                }
//            }
//        }
//        // explicit allow
//        if ($authorized == 0) {
//            foreach ($security->allow as $allow_right => $names) {
//                if (($allow_right == $accessRight)
//                        && $this->isMemberOf($domain, $name, $names)) {
//                    $authorized = 1;
//                    break;
//                }
//            }
//        }
//        // role membership
//        if ($authorized == 0) {
//            foreach ($security->roles as $rolename => $names) {
//                if (in_array($accessRight, $this->getRoleAccessRights($domain, $rolename))
//                        && $this->isMemberOf($domain, $name, $names)) {
//                    $authorized = 1;
//                    break;
//                }
//            }
//        }
//
//        // result
//        if ($authorized == 1) {
//            return true;
//        } else if (!$throwException) {
//            return false;
//        } else {
//            throw new Exception(
//                    sprintf('Access denied (entity: %s, domain: %s, name: %s, right: %s).',
//                    $document->path, $domain, $name, $accessRight));
//        }
    }
}

