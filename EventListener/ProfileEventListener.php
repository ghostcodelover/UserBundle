<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\EventListener;

use ZND\SIM\ApiBundle\EventListener\ApiEventListener;
use ZND\USM\UserBundle\Event\ProfileEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;

/**
 *@DI\Service("znd_user.profile_event_listener")
 */
class ProfileEventListener extends ApiEventListener
{
    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileNew")
     */
    public function onProfileNew(ProfileEvent $event){
      $event->getForm()->setData($event->getProfile());
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileEdit")
     */
    public function onProfileEdit(ProfileEvent $event){
        $event->getForm()->setData($event->getProfile());
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileShow")
     */
    public function onProfileShow(ProfileEvent $event){
       $event->setStatus(Response::HTTP_OK);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfilePatch")
     */
    public function onProfilePatch(ProfileEvent $event){
        $event->setForm($this->process($event->getForm(), $event->getRequest()));
        $event->setStatus(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfilePut")
     */
    public function onProfilePut(ProfileEvent $event){
        $request = $event->getRequest();
        if ($birth_day = $request->request->get('birth_day')){
            $request->request->set('birth_day',new \DateTime($birth_day)) ;
        }
        $event->setForm($this->process($event->getForm(), $event->getRequest()));
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfilePutSuccess")
     */
    public function onProfilePutSuccess(ProfileEvent $event){
         $event->setStatus(Response::HTTP_OK);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileLock")
     */
    public function onProfileLock(ProfileEvent $event)
    {

    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileUnlock")
     */
    public function onProfileUnLock(ProfileEvent $event)
    {

    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileDelete")
     */
    public function onProfileDelete(ProfileEvent $event){

    }

    /**
     * @param \ZND\USM\UserBundle\Event\ProfileEvent $event
     * @DI\Observe("znd_user.onProfileDeleted")
     */
    public function onProfileDeleted(ProfileEvent $event){

    }
}
