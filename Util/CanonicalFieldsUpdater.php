<?php

/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Util;

use ZND\USM\UserBundle\Entity\UserInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class updating the canonical fields of the user.
 *
 * @author mukendi emmanuel
 * @DI\Service("znd_user.user_util_canonical_fields_updater", public= false)
 */
class CanonicalFieldsUpdater
{
    /**
     * @var CanonicalizerInterface
     */
    private $canonicalizer;

    /**
     * CanonicalFieldsUpdater constructor.
     *
     * @param CanonicalizerInterface $canonicalizer
     *
     * @DI\InjectParams({
     *      "canonicalizer" = @DI\Inject("znd_user.user_util_canonicalizer")
     *     })
     */
    public function __construct(CanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * Canonicalizes an email.
     *
     * @param string|null $email
     *
     * @return string|null
     */
    public function canonicalizeEmail($email)
    {
        return $this->canonicalizer->canonicalize($email);
    }

    /**
     * Canonicalizes a username.
     *
     * @param string|null $username
     *
     * @return string|null
     */
    public function canonicalizeUsername($username)
    {
        return $this->canonicalizer->canonicalize($username);
    }
}
