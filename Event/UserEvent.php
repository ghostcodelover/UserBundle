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
use ZND\USM\UserBundle\Entity\UserInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class UserEvent
 * @package ZND\USM\UserBundle\Event
 */
class UserEvent extends ApiEvent
{
    protected $user;
    /**
     * @param UserInterface $user
     * @param FormInterface $form
     * @param Request $request
     * @param Response $response
     */
    public function __construct(UserInterface $user=null, FormInterface $form=null, Request $request=null, Response $response=null)
    {
        parent::__construct($form,$request, $response);
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function setUser(UserInterface $user)
    {
        return $this->user = $user;
    }
}
