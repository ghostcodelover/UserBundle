<?php
namespace ZND\USM\UserBundle\Controller;

use ZND\SIM\ApiBundle\Controller\ApiController;
use ZND\USM\UserBundle\Entity\LocationInterface;
use ZND\USM\UserBundle\Entity\SourceInterface;
use ZND\USM\UserBundle\EntityManager\LocationManagerInterface;
use ZND\USM\UserBundle\EntityManager\SourceManagerInterface;
use UserBundle\Event\LocationEvent;
use ZND\USM\UserBundle\Form\Factory\LocationFormFactoryInterface;
use ZND\USM\UserBundle\Entity\UserInterface;
use FOS\RestBundle\Controller\Annotations as Route;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restful controller for the Activities.
 *
 * @author Mukendi Emmanuel <ghostcodelover@gmail.com>
 */
class LocationController extends ApiController
{

    /**
     * @DI\Inject("events_location.entity_manager")
     * @var LocationManagerInterface
     */
    protected $locationManager;

    /**
     * @var SourceManagerInterface
     * @DI\Inject("events_location.source_entity_manager")
     */
    protected $sourceManager;

    /**
     * @var LocationFormFactoryInterface
     * @DI\Inject("events_location.location_form_factory")
     */
    protected $locationFormFactory;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route\Get("/ssl/location/source/{id}/new")
     */
    public function createAction(Request $request){
        $creator = $this->getUser();
        if (!$creator instanceof UserInterface){
            return  $this->handleView($this->view('not allowed user', Response::HTTP_BAD_REQUEST));
        }
        $location = $this->locationManager->createLocation();
        $form = $this->locationFormFactory->createLocation();
        $event= new LocationEvent($location,$form,$request);
        $this->dispatcher->dispatch("events_location.onLocationCreate", $event);
        if (!$form instanceof FormInterface){
            return $this->handleView($this->view('error while creating form', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return $this->handleView($this->view($form->getData(), Response::HTTP_OK));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route\Post("/ssl/location/source/{id}")
     */
    public function postAction(Request $request, $id){
        $source = $this->sourceManager->findSourceById($id);
        if (!$source instanceof SourceInterface){
            return $this->handleView($this->view('this user is not source', Response::HTTP_BAD_REQUEST));
        }
        $location = $this->locationManager->createLocation();
        $form = $this->locationFormFactory->createLocation();
        $location->setSource($source);
        $event= new LocationEvent($location,$form,$request);
        $this->dispatcher->dispatch("events_location.onLocationCreate", $event);
        $this->dispatcher->dispatch("events_location.onLocationPost", $event);
        if ($form->isValid()){
            $this->locationManager->updateLocation($location);
            $this->dispatcher->dispatch("events_location.onLocationPosSuccess", $event);
            return $this->handleView($this->view($location, Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $id
     * @Route\Get("/location/source/{id}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request, $id){
        $source = $this->sourceManager->findSourceById($id);
        $location = $source->getLocation();
        if (!$location instanceof LocationInterface){
            return $this->handleView($this->view('no location with this id found', Response::HTTP_OK));
        }
        return $this->handleView($this->view( $location, Response::HTTP_OK));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param                                           $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route\Get("/ssl/location/edit/{id}")
     */
    public function editAction(Request $request, $id){
        $location = $this->locationManager->findLocationById($id);
        if (!$location instanceof LocationInterface){
            return $this->handleView($this->view('no locationutor found ', Response::HTTP_NOT_FOUND));
        }
        $form = $this->locationFormFactory->createLocation();
        $event= new LocationEvent($location,$form,$request);
        $this->dispatcher->dispatch("events_location.onLocationEdit", $event);
        if (!$form instanceof FormInterface){
            return $this->handleView($this->view('error while creating form', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return $this->handleView($this->view($form->getData(), Response::HTTP_OK));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param                                           $id
     * @Route\Put("/ssl/location/source/{id}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putAction(Request $request, $id){
        $location = $this->sourceManager->findSourceById($id)->getLocation();
        $form = $this->locationFormFactory->createLocation($location);
        $event= new LocationEvent($location,$form,$request);
        $this->dispatcher->dispatch("events_location.onLocationPut", $event);
        if ($form->isValid()){
            $this->locationManager->updateLocation($location);
            $this->dispatcher->dispatch("events_location.onLocationPutSuccess", $event);
            return $this->handleView($this->view($location, Response::HTTP_CREATED));
        }
        return $this->handleView($this->view('not valid form', Response::HTTP_CREATED));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $id
     * @Route\Delete("/ssl/location/source/{id}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public  function  deleteAction(Request $request, $id){
        $location = $this->sourceManager->findSourceById($id)->getLocation();
        if (!$location instanceof LocationInterface){
            return $this->handleView($this->view('no locationuctor found', Response::HTTP_NOT_FOUND));
        }
        $event = new LocationEvent($location);
        $this->dispatcher->dispatch('events_location.onLocationDelete', $event);
        $this->locationManager->deleteLocation($location);
        $this->dispatcher->dispatch('events_location.onLocationDeleted', $event);
        if (!$event->getStatus() == Response::HTTP_CONFLICT){
            return $this->handleView($this->view('location deleted with success', Response::HTTP_OK));
        }
        return $this->handleView($this->view($location,Response::HTTP_INTERNAL_SERVER_ERROR));
    }

}
