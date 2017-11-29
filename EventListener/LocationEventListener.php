<?php

/**
 * This file is part of the APIsLocationBundle package.
 *
 * (c) Universite Liberte <http://universiteliberte.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace UserBundle\EventListener;


use ZND\SIM\ApiBundle\EventListener\ApiEventListener;
use UserBundle\Entity\LocationInterface;
use UserBundle\EntityManager\LocationManagerInterface;
use UserBundle\Event\LocationEvent;
use UserBundle\Util\LocationIdGeneratorInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;

/**
 * @DI\Service("events_location.location_event_listener")
 * @param LocationEvent $event
 */
class LocationEventListener extends ApiEventListener
{
    /**
     * @var LocationIdGeneratorInterface
     * @DI\Inject("events_location.location_id_generator")
     */
    public $idGenerator;

    /**
     * @var LocationManagerInterface
     * @DI\Inject("events_location.entity_manager")
     */
    public $locationManger;

    /**
     *
     * @DI\Observe("events_location.onLocationCreate")
     * @param LocationEvent $event
     */
    public function onLocationCreate(LocationEvent $event){
        $event->getForm()->setData($event->getLocation());
    }

    /**
     * @DI\Observe("events_location.onLocationPost")
     * @param LocationEvent $event
     */
    public function onLocationPost(LocationEvent $event){
        $form= $this->process($event->getForm(), $event->getRequest(),true);
        $event->setForm($form);
    }

    /**
     * @DI\Observe("events_location.onLocationPostSuccess")
     * @param LocationEvent $event
     */
    public function onLocationPostSuccess(LocationEvent $event){

    }

    /**
     * @DI\Observe("events_location.onLocationGet")
     * @param LocationEvent $event
     */
    public function onLocationGet(LocationEvent $event){

    }

    /**
     * @DI\Observe("events_location.onLocationEdit")
     * @param LocationEvent $event
     */
    public function onLocationEdit(LocationEvent $event){
        $event->getForm()->setData($event->getLocation());
    }

    /**
     * @DI\Observe("events_location.onLocationPut")
     * @param LocationEvent $event
     */
    public function onLocationPut(LocationEvent $event){
        $event->setForm($this->process($event->getForm(), $event->getRequest()));
    }

    /**
     * @DI\Observe("events_location.onLocationPutSuccess")
     * @param LocationEvent $event
     */
    public function onLocationPutSuccess(LocationEvent $event){

    }

    /**
     * @DI\Observe("events_location.onLocationDelete")
     * @param LocationEvent $event
     */
    public function onLocationDelete(LocationEvent $event){

        if ($this->locationManger->findLocationById($event->getLocation()->getId()) instanceof LocationInterface){
            $event->setStatus(Response::HTTP_CONFLICT);
        }
    }

    /**
     * @DI\Observe("events_location.onLocationDeleted")
     * @param LocationEvent $event
     */
    public function onLocationDeleted(LocationEvent $event){

    }
}
