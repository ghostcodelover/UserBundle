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
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 *
 * @author mukendi emmanuel <mukendiemmanuel15@gmail.com>
 * @DI\Service("znd_usm_user.user_util_password_updater", public=false)
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    private $encoderFactory;

    /**
     * PasswordUpdater constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     *
     * @DI\InjectParams({
     *     "encoderFactory" = @DI\Inject("security.encoder_factory.generic")
     *})
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param UserInterface $user
     */
    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();
        if (0 === strlen($plainPassword)) {
            return;
        }
        $encoder = $this->encoderFactory->getEncoder($user);
        if ($encoder instanceof BCryptPasswordEncoder) {
            $user->setSalt(null);
        } else {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $user->setSalt($salt);
        }
        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
    }
}
