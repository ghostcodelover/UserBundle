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
use ZND\SIM\ApiBundle\Repository\ApiRepository;

/**
 * @author mukendi ntenda emmanuel
 */
class ProfileRepository extends ApiRepository
{

    /**
     * @param                                  $page
     * @param                                  $offset
     * @param \ZND\USM\UserBundle\Repository\of $limit
     *
     * @return \ZND\USM\UserBundle\Entity\UserInterface
     * @internal param \ZND\USM\UserBundle\Repository\last $the page of this request $page
     */
    public function getUserByLimit($page, $offset, $limit){
        $qb =  $this->repository
                ->createQueryBuilder('u')
                ->orderBy('a.created_at')
                ->FirstResult($offset)
                ->MaxResult($limit);
        return  $qb->getQuery()->execute();
    }

    /**
     * @param $first_name
     * @param $last_name
     * @return  integer
     */
    public function findUserPosition($first_name, $last_name){
        $qb = $this->createQueryBuilder('p')
              ->select('COUNT (p.id)')
            ->where('p.first_name = :first_name')
            ->andWhere('p.last_name = :last_name')
            ->setParameter('first_name', $first_name)
            ->setParameter('last_name', $last_name);
        return $qb->getQuery()->getSingleScalarResult();
    }
}
