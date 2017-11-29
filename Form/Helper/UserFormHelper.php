<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZND\USM\UserBundle\Form\Helper;
use ZND\SIM\ApiBundle\Form\Helper\ApiFormHelper;

/**
 *
 * @author mukendi emmanuel <mukendiemmanuel@events.cd>
 */
class UserFormHelper extends ApiFormHelper
{
    /**
     * @var string[]
     */
    private static $map = array(
        'ZND\USM\UserBundle\Form\Type\UserFormType' => 'events_user_user_form',
        'ZND\USM\UserBundle\Form\Type\UserGroupFormType' => 'events_user_user_group_form',
    );

    /**
     * UserFormHelper constructor.
     */
    public function __construct()
    {
        parent::__construct(self::$map);
    }
}
