<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Repository;
use Doctrine\ORM\Query;
use ZND\SIM\ApiBundle\Repository\ApiRepository;
use ZND\USM\UserBundle\Entity\UserInterface;

/**
 * @author mukendi ntenda emmanuel
 *
 */
class UserRepository extends ApiRepository
{


    /**
     * @param bool $byLimit
     * @param int $offset
     * @param int $limit
     *
     * @return array|Query
     */
    public function findUsers($byLimit, $offset, $limit){
        if ($byLimit){
            $queryBuilder= $this->createQueryBuilder("user_repository");
            return $this->handle($queryBuilder, $offset, $limit);
        }
        return parent::findAll();
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     *
     * @return mixed
     */
    public function findUserOptions(UserInterface $user){
        $query= $this->createQueryBuilder("u")
                 ->leftJoin("u.options", "options")
                 ->where("u.id = :id")
                 ->setParameter("id",$user->getId());
        return $this->handleSingle($query);
    }

    /**
     * @param $token
     *
     * @return array
     */
    public function getUserWithAccessToken($token){
      $qb = $this->createQueryBuilder('u')
                 ->leftJoin('u.accessToken', 'token')
                 ->where('token.token = :token ')
                 ->setParameter('token',$token)
                 ->setMaxResults(1);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $uid
     *
     * @return mixed
     */
    public function findUserByUid($uid){
        $qb = $this->createQueryBuilder('u')
                    ->leftJoin('u.profile', 'profile')
                    ->where('profile.guid = :uid')
                    ->setParameter('uid', $uid);
        return $qb->getQuery()->getSingleResult();
    }

}
