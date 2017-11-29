<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Controller;


use ZND\SIM\ApiBundle\Controller\ApiController;
use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\Entity\ProfileInterface;
use ZND\USM\UserBundle\EntityManager\UserEntityManagerInterface;
use ZND\USM\UserBundle\EntityManager\ProfileEntityManagerInterface;
use ZND\USM\UserBundle\Event\ProfileEvent;
use ZND\USM\UserBundle\Flash\FlashInterface;
use ZND\USM\UserBundle\Form\Factory\UserFormFactoryInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the registration
 *
 * @author Mukendi emmanuel <mukendiemmanuel15@gmail.com>
 */
class ProfileController extends ApiController
{
    /**
     *@var ProfileEntityManagerInterface
     * @DI\Inject("znd_usm_user.profile_entity_manager")
     */
    protected $profileManager;

    /**
     *@var UserEntityManagerInterface
     * @DI\Inject("znd_usm_user.user_entity_manager")
     */
    protected $userEntityManager;

    /**
     *@DI\Inject("znd_usm_user.user_flash")
     * @var FlashInterface;
     */
    protected $flash;
    /**
     * @var UserFormFactoryInterface
     * @DI\Inject("znd_usm_user.user_form_factory")
     */
    private  $profileFormFactory;

    /**
     * "get_profile"      [GET] /profiles/{username}
     *
     * @param Request $request
     *
     * @internal param $username
     * @Rest\Get( "/profiles")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $uid = $request->query->get('uid');
        $profile = $this->profileManager->findProfileByUid($uid);
        if(!is_object($profile) || !$profile instanceof ProfileInterface){
            return $this->handleView($this->view($uid, Response:: HTTP_NOT_FOUND));
        }
        $event = new ProfileEvent($profile,null,$request);
        $this->dispatcher->dispatch("znd_usm_user.onProfileShow",$event);
        if ($event->getStatus()!==Response::HTTP_OK){
            return $this->handleView($this->view('no profile found with this id:'.$uid, Response:: HTTP_NOT_FOUND));
        }
        return $this->handleView($this->view($profile, Response::HTTP_OK));
    }

    /**
     * "patch_profile"    [PATCH] /profiles
     * @param Request $request
     * @param \ZND\USM\UserBundle\Controller\username $
     *
     * @return mixed
     * @Rest\Patch("/api/users/profiles/patch")
     */
    public function patchAction(Request $request)
    {   $username= $request->query->get("username");
        $profile= $this->profileManager->findProfileById($username);
        if (!$profile instanceof ProfileInterface){
            return $this->handleView($this->view($this->flash->getMessage('test','error'), Response::HTTP_BAD_REQUEST));
        }
        $form = $this->profileFormFactory->createProfile($profile);
        $event = new ProfileEvent($profile, $form, $request);
        $form= $event->getForm();
        $this->dispatcher->dispatch("znd_usm_user.onProfilePatch",  $event);
        if(!$event->getStatus()){
            return $this->handleView($this->view($this->flash->getMessage('patch','error'), Response::HTTP_BAD_REQUEST));
        }
        return $this->handleView($this->view($form->getData(), Response::HTTP_OK));
    }


    /**
     * "post_profiles"    [GET] /profiles   ok
     * @param Request $request
     *
     * @return mixed
     * @Rest\Get("/api/users/profiles/edit")
     */
    public function editAction(Request $request)
    {   $profile= $this->profileManager->findProfileById($request->query->get('username'));
        $form = $this->profileFormFactory->createProfile($profile);
        $event = new ProfileEvent($profile, $form, $request);
        $this->dispatcher->dispatch('znd_usm_user.onProfileNew', $event);
        if ($form instanceof FormInterface){
            return $this->handleView($this->view($form->createView(), Response::HTTP_OK));
        }
        return $this->handleView($this->view($form->getErrors(),Response::HTTP_INTERNAL_SERVER_ERROR));
    }
    /**
     * "put_profile"      [PUT] /profiles/{username}
     *
     * @param Request $request
     *
     * @return mixed
     * @Rest\Put("/api/profiles/ssl")
     */
    public function putAction(Request $request)
    {
        $id= $request->query->get('uid');
        $profile = $this->profileManager->findProfileByUid($id);
        if((!is_object($profile) || !$profile instanceof ProfileInterface)){
            return $this->handleView($this->view($this->flash->getMessage('put','error',['username'=>$id]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $form = $this->profileFormFactory->createProfile();
        $event = new ProfileEvent($profile,$form, $request);
        $this->dispatcher->dispatch('znd_usm_user.onProfileEdit',  $event);
        $this->dispatcher->dispatch('znd_usm_user.onProfilePut',  $event);
        if ($form->isValid()) {
            $this->dispatcher->dispatch('znd_usm_user.onProfilePutSuccess', $event);
            $this->profileManager->updateProfile($profile);
            return $this->getViewHandler()->handle($this->view($profile, Response::HTTP_OK));
        }
        return $this->getViewHandler()->handle($this->view($form->getErrors(), Response::HTTP_INTERNAL_SERVER_ERROR));
    }


    /**
     * "lock_profile"     [PATCH] /profiles/lock/{username}
     *
     * @param Request $request
     * @param                                           $username
     *
     * @return mixed
     * @Rest\Lock("/api/users/profiles")
     */
    public function lockAction(Request $request, $username)
    {
        $profile = $this->profileManager->findProfileById($username);
        if(!is_object($profile) || !$profile instanceof ProfileInterface && $profile->getProfilename()!=$username){
            return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','error',['username'=>$username]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $event = new ProfileEvent($profile, $request);
        $this->dispatcher->dispatch("znd_usm_user.onProfileLock", $event);
        if(!$profile->isLocked()){
            return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','error',['username'=>$username]), Response::HTTP_BAD_REQUEST));
        }
        $this->profileManager->updateProfile($profile);
        return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','success',['username'=>$username]), Response::HTTP_LOCKED));
    }

    /**
     * @param Request $request
     *
     * @internal param Request $username
     * @Rest\Unlock("/api/users/profiles")
     */
    public function unlockAction(Request $request){

    }

    /**
     * @param Request $request
     *
     * @internal param Request $username
     * @Rest\Delete("/api/users/profiles")
     */
    public function deleteAction(Request $request){

    }
}