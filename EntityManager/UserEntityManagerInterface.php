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
use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\Entity\UserOptionsInterface;

/**
 * Interface to be implemented by user managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to users should happen through this interface.
 *
 * The class also contains ACL annotations which will only work if you have the
 * SecurityExtraBundle installed, otherwise they will simply be ignored.
 *
 * @author Gordon Franke <info@nevalon.de>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface UserEntityManagerInterface
{

    /**
     * Creates an empty user instance.
     *
     * @return UserInterface
     */
    public function createUser();

    /**
     * @param array $criteria
     *
     * @return \ZND\USM\UserBundle\Entity\UserInterface
     */
    public function findUserBy(array $criteria);

    /**
     * @param $email
     *
     * @return UserInterface
     */
    public function findUserByEmail($email);


    /**
     * @param $phone
     *
     * @return UserInterface
     */
    public function findUserByPhone($phone);


    /**
     * @param $username
     *
     * @return UserInterface
     */
    public function findUserByUsername($username);

    /**
     * @param $uid
     *
     * @return UserInterface
     */
    public function findUserByUid($uid);

    /**
     * Finds a user by its username or email.
     *
     * @param string $id
     *
     * @return UserInterface | null
     */
    public function findUserById($id);

    /**
     * @param string $token
     *
     * @return UserInterface|object
     */
    public  function findUserByToken($token);

    /**
     * @param $token
     *
     * @return UserInterface|object
     */
    public function findUserByAccessToken($token);

    /**
     * @return mixed
     */
    public function findUsersCount();

    /**
     * @param $byLimit
     * @param $offset
     * @param $limit
     *
     * * @return UserInterface[]
     */
    public function findUsers($byLimit=false, $offset, $limit);


    /**
     * Reloads a user.
     *
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user);

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     */
    public function deleteUser(UserInterface $user);

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     */
    public function updateUser(UserInterface $user);

    /**
     * Updates the canonical username and email fields for a user.
     *
     * @param UserInterface $user
     */
    public function updateCanonicalFields(UserInterface $user);

    /**
     * Updates a user password if a plain password is set.
     *
     * @param UserInterface $user
     */
    public function updatePassword(UserInterface $user);

    /**
     * @param UserInterface $user
     *
     * @return UserOptionsInterface
     */
    public function findUserOptions(UserInterface $user);

    public  function getClass();
}
