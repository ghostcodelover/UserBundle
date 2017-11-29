<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Event;

use ZND\SIM\ApiBundle\Event\ApiEvent;
use ZND\USM\UserBundle\Entity\ProfileInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileEvent
 *
 * @package ZND\USM\UserBundle\Event
 */
class ProfileEvent extends ApiEvent
{
    private $profile;

    /**
     * @param \ZND\USM\UserBundle\Entity\ProfileInterface $profile
     * @param \Symfony\Component\Form\FormInterface          $form
     * @param \Symfony\Component\HttpFoundation\Request      $request
     * @param \Symfony\Component\HttpFoundation\Response     $response
     * @param bool                                           $status
     *
     * @internal param \ZND\USM\UserBundle\Entity\ProfileInterface $user
     */
    public function __construct(ProfileInterface $profile=null,FormInterface $form=null, Request $request=null, Response $response=null, $status=false)
    {
        parent::__construct($form,$request,$response);

        $this->profile= $profile;
    }


    /**
     * @return ProfileInterface
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\ProfileInterface $profile
     *
     * @return \ZND\USM\UserBundle\Entity\ProfileInterface
     *
     */
    public function setProfile(ProfileInterface $profile)
    {
        return $this->profile = $profile;
    }
}
