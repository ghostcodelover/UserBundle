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

use ZND\SIM\ApiBundle\EntityManager\ApiEntityManager;
use ZND\SIM\ApiBundle\Persistence\ApiObjectManagerInterface;
use ZND\USM\UserBundle\Entity\ProfileInterface;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("znd_usm_user.profile_entity_manager")
 */
class ProfileEntityManager extends ApiEntityManager  implements ProfileEntityManagerInterface
{

    /**
     * ProfileEntityManager constructor.
     * @DI\InjectParams({
     *     "om"    = @DI\Inject("events_api.api_object_manager"),
     *     "class"  = @DI\Inject("%znd_usm_user.profile_entity_class%")
     * })
     *
     * @param \ZND\SIM\ApiBundle\Persistence\ApiObjectManagerInterface $om
     * @param                                                         $class
     */
    public function __construct(ApiObjectManagerInterface $om, $class)
    {
        parent::__construct($om, $class);
    }

    /**
     * @param array $criteria
     *
     * @return object | ProfileInterface
     */
    public function findProfileBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @inheritdoc
     */
    public function findProfileById($profileId){
        return $this->findProfileBy(["id"=> $profileId]);
    }

    /**
     * @param string $uid
     *
     * @return ProfileInterface
     */
    public function findProfileByUid($uid){
       return $this->findProfileBy(array('guid'=>$uid));
    }

    /**
     * {@inheritdoc}
     */
    public function findUsersProfiles()
    {
        return $this->repository->findAll();
    }

    /**
     *
     * @return ProfileInterface
     */
    public function createProfile(){
        return $this->factory();
    }

    /**
     * @param $first_name
     * @param $last_name
     *
     * @return integer
     */
    public function  getPosition($first_name, $last_name){
        return $this->repository->findUserPosition($first_name, $last_name);
    }

    /**
     * {@inheritdoc}
     */
    public function reloadProfile(ProfileInterface $profile)
    {
        $this->om->refresh($profile);
    }

    /**
     * {@inheritdoc}
     */
    public function updateProfile(ProfileInterface $profile)
    {
        $this->om->persist($profile);
        $this->om->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProfile(ProfileInterface $profile)
    {
        $this->om->remove($profile);
        $this->om->flush();
    }

    public  function getClass(){
        return $this->class;
    }
}
