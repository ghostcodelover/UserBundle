<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

/**
 * Created by PhpStorm.
 * User: localgit
 * Date: 1/10/17
 * Time: 12:14 AM
 */

namespace ZND\USM\UserBundle\Entity;


/**
 * Interface ProfileInterface
 *
 * @package ZND\USM\UserBundle\Entity
 */
interface ProfileInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getGuId();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $last_name
     */
    public function setLastName($last_name);

    /**
     * @return string
     */
    public function getGender();

    /**
     * @param string $gender
     */
    public function setGender($gender);

    /**
     * @return \DateTime
     */
    public function getBirthDay();

    /**
     * @param string $birth_day
     */
    public function setBirthDay($birth_day);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     */
    public function setCountry($country);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

}