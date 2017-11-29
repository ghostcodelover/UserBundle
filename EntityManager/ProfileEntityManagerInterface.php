<?php

/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\EntityManager;

use ZND\USM\UserBundle\Entity\ProfileInterface;

/**
 * Interface to be implemented by user managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to users should happen through this interface.
 *
 * The class also contains ACL annotations which will only work if you have the
 * SecurityExtraBundle installed, otherwise they will simply be ignored.
 *
 * @author Mukendi emmanuel <mukendiemmanuel@events.cd>
 */
interface ProfileEntityManagerInterface
{
    /**
     * @param array $criteria
     *
     * @return ProfileInterface
     */
    public function findProfileBy(array $criteria);

    /**
     * @param string $profileId
     *
     * @return ProfileInterface
     */
    public function findProfileById($profileId);

    /**
     * @param string $uid
     *
     * @return ProfileInterface
     */
    public function findProfileByUid($uid);

    /**
     * @return ProfileInterface[]
     */
    public function findUsersProfiles();

    /**
     *
     * @return \ZND\USM\UserBundle\Entity\ProfileInterface
     */
    public function createProfile();

    /**
     * @param $first_name
     * @param $last_name
     *
     * @return integer
     */
    public function  getPosition($first_name, $last_name);

    /**
     * @param \ZND\USM\UserBundle\Entity\ProfileInterface $profile
     *
     * @return
     */
    public function reloadProfile(ProfileInterface $profile);

    /**
     * @param \ZND\USM\UserBundle\Entity\ProfileInterface $profile
     *
     * @return
     */
    public function updateProfile(ProfileInterface $profile);

    /**
     * @param \ZND\USM\UserBundle\Entity\ProfileInterface $profile
     *
     * @return
     */
    public function deleteProfile(ProfileInterface $profile);
}
