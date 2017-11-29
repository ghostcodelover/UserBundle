<?php

/**
 * This file is part of the APIsLocationBundle package.
 *
 * (c) Universite Liberte <http://universiteliberte.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace UserBundle\Event;
use ZND\SIM\ApiBundle\Event\ApiEvent;
use ZND\USM\UserBundle\Entity\LocationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class LocationEvent
 * An event that occurs related to a location.
 *
 * @author Mukendi Emmanuel <mukendiemmanuel15@gmail.com>
 * @package UserBundle\Event
 */
class LocationEvent extends ApiEvent
{
    /**
     * @var LocationInterface
     */
    private $location;

    public function __construct(LocationInterface $location,FormInterface $form=null, Request $request=null, Response $response=null)
    {
        parent::__construct($form, $request, $response);
        $this->location = $location;
    }

    /**
     * @return \ZND\USM\UserBundle\Entity\LocationInterface
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\LocationInterface $location
     */
    public function setLocation(LocationInterface $location)
    {
        $this->location = $location;
    }
}
