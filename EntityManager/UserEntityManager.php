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

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use ZND\SIM\ApiBundle\EntityManager\ApiEntityManager;
use ZND\SIM\ApiBundle\Persistence\ApiObjectManagerInterface;
use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\Util\CanonicalFieldsUpdater;
use ZND\USM\UserBundle\Util\PasswordUpdaterInterface;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * @DI\Service("znd_usm_user.user_entity_manager")
 */
class UserEntityManager extends ApiEntityManager  implements UserEntityManagerInterface
{
    /**
     * @var passwordUpdaterInterface
     */
    protected $passwordUpdater;

    /**
     * @var canonicalFieldsUpdater
     */
    protected $canonicalFieldsUpdater;

    /**
     * Constructor.
     *
     * @param PasswordUpdaterInterface                                                            $passwordUpdater
     * @param CanonicalFieldsUpdater                                                              $canonicalFieldsUpdater
     *
     * @param ApiObjectManagerInterface $om
     * @param                                                                                     $class
     * @DI\InjectParams({
     *     "passwordUpdater" = @DI\Inject("znd_usm_user.user_util_password_updater"),
     *     "canonicalFieldsUpdater"= @DI\Inject("znd_usm_user.user_util_canonical_fields_updater"),
     *     "om"    = @DI\Inject("events_api.api_object_manager"),
     *     "class" = @DI\Inject("%znd_usm_user.user_entity_class%")
     * })
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater,
                                CanonicalFieldsUpdater $canonicalFieldsUpdater,
                                ApiObjectManagerInterface $om, $class)
    {
        parent::__construct($om, $class);
        $this->passwordUpdater= $passwordUpdater;
        $this->canonicalFieldsUpdater= $canonicalFieldsUpdater;
    }

    /**
     * @param array $criteria
     *
     * @return object|UserInterface
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param bool $byLimit
     * @param      $offset
     * @param      $limit
     *
     * @return mixed|UserInterface[]
     */
    public function findUsers($byLimit =false, $offset, $limit)
    {
        return $this->repository->findUsers($byLimit, $offset,$limit);
    }

    /**
     * @param $email
     *
     * @return \ZND\USM\UserBundle\Entity\UserInterface|object
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)]);
    }

    /**
     * @param $phone
     *
     * @return \ZND\USM\UserBundle\Entity\UserInterface|object
     */
    public function findUserByPhone($phone)
    {
        return $this->findUserBy(['phone' => $phone]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(['usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)]);
    }

    /**
     * @param $uid
     *
     * @return UserInterface
     */
    public function findUserByUid($uid)
    {
        return $this->repository->findUserByUid($uid);
    }


    public function findUserByAccessToken($token){
        return $this->repository->getUserWithAccessToken($token);
    }

    /**
     * @param string $id
     *
     * @return \ZND\USM\UserBundle\Entity\UserInterface|null|object
     */
    public function findUserById($id) {
        if (preg_match('/^.+\@\S+\.\S+$/', $id)) {
            return $this->findUserByEmail($id);
        }
        else if($this->findUserByPhone($id) instanceof UserInterface){
            return $this->findUserByPhone($id);
        }else if ($this->findUserBy(array('id'=>$id))instanceof UserInterface){
            return $this->findUserBy(array('id'=>$id));
        }else{
            return $this->findUserByUsername($id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByToken($token)
    {
        if (6 == trim(strlen($token))){
            return $this->findUserBy(['confirmationCode' => $token]);
        }
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    /**
     * @return int
     */
    public function findUsersCount()
    {
        return count($this->repository->findAll());
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        return new $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->om->refresh($user);
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        $this->om->remove($user);
        $this->om->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);
        $this->om->persist($user);
        if ($andFlush) {
            $this->om->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function findUserOptions(UserInterface $user)
    {
        return $this->repository->findUserOptions($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(UserInterface $user)
    {
        $this->passwordUpdater->hashPassword($user);
    }

    public  function getClass(){
        return $this->class;
    }
}
