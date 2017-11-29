<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Test\Controller;


use ZND\SIM\ApiBundle\Controller\ApiController;
use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\EntityManager\UserEntityManagerInterface;
use ZND\USM\UserBundle\Event\UserEvent;
use ZND\USM\UserBundle\Flash\FlashInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the registration
 *
 * @author Mukendi emmanuel <mukendiemmanuel15@gmail.com>
 */
class UserController extends ApiController
{
    /**
     *@var UserEntityManagerInterface
     * @DI\Inject("znd_usm_user.user_entity_manager")
     */
    protected $userManager;

    /**
     *@var UserInterface $user
     */
    private $user;
    /**
     *@DI\Inject("znd_usm_user.user_flash")
     * @var FlashInterface $flasInfo;
     */
    private $flasInfo;

    /**
     *@var FormInterface $form
     */
    private $form;


    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $usernameOrEmail
     *
     * @internal param \Symfony\Component\HttpFoundation\Request $request
     */
    public function testAction($id){
        if(!$this->userManager->findUserById($id)instanceof UserInterface){
            return $this->handleView($this->view($this->flasInfo->getMessage('test','error', ['slug'=>$id]),Codes::HTTP_NOT_FOUND ));
        }
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('edit', 'success',['slug'=>$id]),Codes::HTTP_OK));
    }

    /**
     * "new_users"     [GET] /users/new   ok
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function createAction(Request $request)
    {
        $user = $this->userManager->createUser();
        if(!$user instanceof UserInterface){
            return $this->handleView($this->view(["message"=>"bonjour"], Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $this->form = $this->formFactory->createForm("znd_usm_user.user_form_type");
        $event = new UserEvent($user,$this->form, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_NEW, $event);
        return $this->handleView($this->view($this->flasInfo->getMessage('new','success',[]),Codes::HTTP_OK));
    }

    /**
     * "post_users"    [POST] /users   ok
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function cpostAction(Request $request)
    {   $form = $this->formFactory->createForm("znd_usm_user.user_form_type");
        if(!$form instanceof FormInterface){
            return $this->handleView($this->view($this->flasInfo->getMessage('post','error'), Codes::HTTP_FAILED_DEPENDENCY));
        }
        $event = new UserEvent(null, $form, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_POST, $event);
        if ($form->isValid()){
            $event = new UserEvent(null, $form, $request);
            $this->dispatcher->dispatch(Events::EVENTS_USER_POST_SUCCESS, $event);
            $this->userManager->updateUser($event->getUser());
            return $this->handleView($this->view($this->flasInfo->getMessage('post','success',['username'=>$event->getUser()->getUsername()]), Codes::HTTP_CREATED));
        }
        return $this->handleView($this->view($this->flasInfo->getMessage('post','error'),$event->getStatus()));
    }

    /**
     * "get_user"      [GET] /users/{username}
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function getAction(Request $request, $username)
    {
        $user = $this->userManager->findUserById($username);
        if(!is_object($user) || !$user instanceof UserInterface){
            return $this->handleView($this->view($this->flasInfo->getMessage('get', 'error',['username'=>$username]), Codes:: HTTP_NOT_FOUND));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_GET, $event);
        return $this->handleView($this->view($user, Codes::HTTP_OK));
    }

    /**
     * "get_users"     [GET] /users
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function cgetAction(Request $request)
    {
        $limit = 10;
        $page= $request->query->get("page");
        $offset= $request->query->get("offset");
        $offset = $this->paginator->getOffset($page, $limit, $offset);
        $users = $this->userManager->findUsers($offset, $limit);
        if(!$users){
            return $this->handleView($this->view($this->flasInfo->getMessage('cget','error'), Codes::HTTP_NOT_FOUND));
        }
        foreach ($users as $user) {
            $this->dispatcher->dispatch(Events::EVENTS_USER_CGET, new UserEvent($user, $request));
        }
        $page=$this->paginator->getTotalPage($this->userManager->findUsersCount(),$limit);

        return $this->handleView($this->view(["users"=>$users, "page"=>$page], Codes::HTTP_OK));
    }


    /**
     * call this action to verifies if a user with the usernameOrEmail passed exist
     * "check user"     [GET] /users/check/{usernameOrEmail}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $email
     *
     * @return mixed
     * @internal param $usernameOrEmail
     *
     */
    public function checkAction(Request $request, $email){
        $user = $this->userManager->findUserById($email);
        if($user instanceof UserInterface){
            if(!$user->isIsMailChecked()){
                $event= new UserEvent($user,$request);
                $this->dispatcher->dispatch(Events::EVENTS_USER_CHECK, $event);
                $this->userManager->updateUser($user);
            }
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('post','success',['email'=>$email]), Codes::HTTP_FOUND));
        }
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('post','error',['email'=>$email]), Codes::HTTP_NOT_FOUND));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmAction(Request $request, $token){
        $user= $this->userManager->findUserByConfirmationTokenOrCode($token);
        if(!is_object($user) || !$user instanceof UserInterface){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('confirm','error', ['token'=>$token]), Codes::HTTP_IM_USED));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_CONFIRM, $event);
        if (!$event->getStatus()) {
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('confirm','error', ['token'=>$token]), Codes::HTTP_IM_USED));
        }
        $this->dispatcher->dispatch(Events::EVENTS_USER_CONFIRMED, new UserEvent($user, $request));
        return $this->getViewHandler()->handle($this->view($user,Codes::HTTP_ACCEPTED));
    }

    /**
     * "patch_user"    [PATCH] /users
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \ZND\USM\UserBundle\Controller\username $
     *
     * @return mixed
     */
    public function patchAction(Request $request)
    { $user= $this->getUser();
        if (!$user instanceof UserInterface){
            return $this->getViewHandler()->handle($this->flasInfo->getMessage('patch','error'), Codes::HTTP_BAD_REQUEST);
        }
        $form = $this->formFactory->createForm($user);
        $event = new UserEvent($user, $form, $request);
        $form= $event->getForm();
        $this->dispatcher->dispatch(Events::EVENTS_USER_PATCH,  $event);
        if(!$event->getStatus()){
            return $this->getViewHandler()->handle($this->flasInfo->getMessage('patch','error'), Codes::HTTP_BAD_REQUEST);
        }
        return $this->getViewHandler()->handle($this->view($form->getData(), Codes::HTTP_OK));
    }

    /**
     * Change user password.
     *
     * @param Request $request
     *
     * @param         $username
     *
     * @return Response
     */
    public function changeAction(Request $request, $username)
    {
        $this->user = $this->getUser();
        if (!is_object($this->user) || !$this->user instanceof UserInterface || $this->user->getUsername()!=$username) {
            return $this->handleView($this->view($username, Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $this->form = $this->formFactory->createForm('znd_usm_user.user_password_form');
        $event = new UserEvent($this->user,$this->form, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_CHANGE, $event);
        $code= Codes::HTTP_OK;
        if ($event->getStatus()){
            $code= Codes::HTTP_INTERNAL_SERVER_ERROR;
        }
        return $this->handleView($this->view($this->form->createView(), $code));
    }

    /**
     * "edit_user"     [GET] /users/edit/{username}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function editAction(Request $request,$username)
    {
        $this->user = $this->getUser();
        if(!is_object($this->user) || !$this->user instanceof UserInterface && $this->user->getUsername()===$username){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('edit','error', ['username'=>$username]), Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $event = new UserEvent($this->user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_EDIT, $event);
        $this->form= $this->formFactory->createForm($this->user);
        if(!$this->form|| !$this->form instanceof FormInterface){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('edit', 'error',['username'=>$username]), Codes::HTTP_BAD_REQUEST));
        }
        return $this->getViewHandler()->handle($this->view($this->form->createView(), Codes::HTTP_OK));
    }

    /**
     * "put_user"      [PUT] /users/{username}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function putAction(Request $request, $username)
    {
        if((!is_object($this->user) || !$this->user instanceof UserInterface) &&$username != $this->user->getUsername()){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('put','error',['username'=>$username]), Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $event = new UserEvent($this->form, $this->user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_PUT,  $event);
        if ($this->form->isValid()) {
            $this->dispatcher->dispatch(Events::EVENTS_USER_PUT_SUCCESS, $event);
            if ($event->getStatus()===true){
                $this->userManager->updateUser($this->user);
                return $this->getViewHandler()->handle($this->view($this->user, Codes::HTTP_CREATED));
            }
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('put','error',['username'=>$username]), Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('put','error',['username'=>$username]), Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
    }


    /**
     * "lock_user"     [PATCH] /users/lock/{username}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function lockAction(Request $request, $username)
    {
        $user = $this->getUser();
        if(!is_object($user) || !$user instanceof UserInterface && $user->getUsername()!=$username){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('lock','error',['username'=>$username]), Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_LOCK, $event);
        if(!$user->isLocked()){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('lock','error',['username'=>$username]), Codes::HTTP_BAD_REQUEST));
        }
        $this->userManager->updateUser($user);
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('lock','success',['username'=>$username]), Codes::HTTP_LOCKED));
    }


    /**
     * "remove_user"     [GET] /users/ban/{username}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function removeAction(Request $request, $username)
    {
        $user = $this->getUser();
        if(!is_object($user) || !$user instanceof UserInterface && $user->getUsername()==$username){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('remove','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_REMOVE, $event);
        if(!$user->isBanied()){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('remove','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $this->userManager->updateUser($user);
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('remove','success',['username'=>$username]), Codes::HTTP_OK));
    }

    /**
     * "remove_user"     [GET] /users/ban/{username}
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return mixed
     */
    public function restoreAction(Request $request, $username)
    {
        $user = $this->getUser();
        if(!is_object($user) || !$user instanceof UserInterface && $user->getUsername()==$username){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('restore','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_RESTOR, $event);
        if(!$user->isRemoved()){
            return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('restore','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $this->userManager->updateUser($user);
        return $this->getViewHandler()->handle($this->view($this->flasInfo->getMessage('restore','success',['username'=>$username]), Codes::HTTP_OK));
    }


    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkAction(Request $request, $username){
        $user = $this->getUser();
        $followed= $this->userManager->findUserById($username);
        if(!is_object($user) || !$user instanceof UserInterface || !is_object($followed)){
            return $this->handleView($this->view($this->flasInfo->getMessage('link','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_LINK, $event);
        if (!$this->userManager->isAllReadyFollower($followed, $user)){
            $this->userManager->startFollowingUser($user, $followed);
            return $this->handleView($this->view($user, Codes::HTTP_ACCEPTED));
        }
        return $this->handleView($this->view($this->flasInfo->getMessage('restore','success',['username'=>$username]), Codes::HTTP_OK));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function unlinkAction(Request $request, $username){
        $user = $this->getUser();
        $followed= $this->userManager->findUserById($username);
        if(!is_object($user) || !$user instanceof UserInterface || !is_object($followed)){
            return $this->handleView($this->view($this->flasInfo->getMessage('link','error',['username'=>$username]), Codes::HTTP_INTERNAL_SERVER_ERROR));
        }
        $event = new UserEvent($user, $request);
        $this->dispatcher->dispatch(Events::EVENTS_USER_LINK, $event);
        if (!$this->userManager->isAllReadyFollower($followed, $user)){
            $this->userManager->stopFollowingUser($user, $followed);
            return $this->handleView($this->view($user, Codes::HTTP_ACCEPTED));
        }
        return $this->handleView($this->view($this->flasInfo->getMessage('restore','success',['username'=>$username]), Codes::HTTP_BAD_REQUEST));
    }

    /**
     * @param                                           $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function optionsAction($username){
        $user = $this->userManager->findUserById($username);
        if (!$user || !$user instanceof UserInterface){
            return $this->handleView($this->view('u have to be connect', Codes::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $options= $this->userManager->findUserOptions($user);
        return $this->handleView($this->view($options, Codes::HTTP_OK));
    }
}