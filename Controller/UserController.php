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
use ZND\USM\UserBundle\Event\UserEvent;
use ZND\USM\UserBundle\Flash\FlashInterface;
use ZND\USM\UserBundle\Form\Factory\UserFormFactoryInterface;
use FOS\RestBundle\Controller\Annotations as Route;
use JMS\DiExtraBundle\Annotation as DI;
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
    *@DI\Inject("znd_user.user_flash")
     * @var FlashInterface;
    */
    protected $flash;
    /**
     * @var UserFormFactoryInterface
     * @DI\Inject("znd_user.user_form_factory")
     */
    private  $userFormFactory;

    /**
     * @param Request $request
     *
     * @return Response
     * @internal param $id
     *
     * @internal param $usernameOrEmail
     * @Route\Get("/users/test")
     */
    public function testAction(Request $request){
        $id= $request->query->get('id');
        if(!$this->userManager->findUserById($id)instanceof UserInterface){
            $message= $this->flash->getMessage('test','error', ['slug'=>$id]);
            return $this->handleView($this->view(array('message'=>$message),Response::HTTP_NOT_FOUND ));
        }
        $message= $this->flash->getMessage('test','success', ['slug'=>$id]);
        return $this->handleView($this->view(array('message'=>$message),Response::HTTP_OK));
    }

    /**
     * @return Response
     *
     * @Route\Get("/users/info")
     */
    public function connectedAction(){
        $response  = array('inline'=>false );
        if ($this->getUser() instanceof UserInterface){
            $user =$this->getUser();
            $response = array('inline'=>true, 'username'=> $user->getUsername(), 'avatar_id'=>$user->getAvatar()->getId());
        }
        return $this->handleView($this->view($response, Response::HTTP_OK));
    }


    /**
     * "post_users"    [GET] /users   ok
     *
     * @param Request $request
     *
     * @Route\Get("/users/new")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        $form = $this->userFormFactory->createUser();
        $user= $this->userManager->createUser();
        $event = new UserEvent($user, $form, $request);
        $this->dispatcher->dispatch('znd_user.onUserNew', $event);
        if ($event->getStatus()==Response::HTTP_OK){
            return $this->handleView($this->view($form->getData(), Response::HTTP_OK));
        }
        return $this->handleView($this->view($form->getErrors(),Response::HTTP_BAD_REQUEST));
    }

    /**
     * "post_users"    [POST] /users   ok
     * @param Request $request
     *
     * @return mixed
     * @Route\Post("/users")
     */
    public function postAction(Request $request)
    {
        $form = $this->userFormFactory->createUser();
        $user= $this->userManager->createUser();
        $event = new UserEvent($user, $form, $request);
        $this->dispatcher->dispatch('znd_user.onUserNew', $event);
        $this->dispatcher->dispatch('znd_user.onUserPost', $event);
        if ($form->isValid()){
            $this->dispatcher->dispatch('znd_user.onUserPostSuccess', $event);
            if (is_string($user->getConfirmationCode())){
                $this->userManager->updateUser($user);
                $this->dispatcher->dispatch('znd_user.onUserPosted', $event);
                return $this->handleView($this->view(array('email'=>$user->getEmail()),Response::HTTP_CREATED));
            }
        }
        return $this->handleView($this->view($form->all(), Response::HTTP_INTERNAL_SERVER_ERROR));
    }


    /**
     * "check user"     [GET] /users/check
     *
     * @param Request $request
     *
     *
     * @Route\Patch("/users/check_mail")
     *
     * @return Response
     */
    public function checkEmailAction(Request $request){
        $email = $request->query->get('email');
        $user = $this->userManager->findUserByEmail($email);
        if($user instanceof UserInterface){
            if(!empty($user->getConfirmationCode())){
                if(!$user->isIsMailChecked()){
                    $event= new UserEvent($user,null,$request);
                    $this->dispatcher->dispatch("znd_user.onUserCheck", $event);
                    $this->userManager->updateUser($user);
                }
                $message= $this->flash->getMessage('check','success',['email'=>$email]);
                return $this->handleView($this->view(array('error'=>false,'email'=>$user->getEmail(),'message'=>$message, 'code'=>$user->getConfirmationCode()), Response::HTTP_OK));
            }
        }
        $message = $this->flash->getMessage('check','error',['email'=>$email]);
        return $this->handleView($this->view(array("message"=>$message, "error"=> true), Response::HTTP_OK));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route\Get("/users/confirm")
     */
    public function confirmUserAction(Request $request){
        $token = $request->query->get('token');
        $user = $this->userManager->findUserByToken($token);
        if(!is_object($user) || !$user instanceof UserInterface){
            $message= $this->flash->getMessage('confirm','error', ['slug'=>$token]);
            return $this->handleView($this->view(array('message'=>$message,'error'=>true), Response::HTTP_OK));
        }
        $event = new UserEvent($user,null, $request);
        if (!$user->isIsConfirmed()){
            $this->dispatcher->dispatch("znd_user.onUserConfirm", $event);
            $this->dispatcher->dispatch("znd_user.onUserConfirmed", $event);
            $this->userManager->updateUser($user);
            $message = $this->flash->getMessage('confirm','success',["username"=>$user->getUsername()]);
            return $this->handleView($this->view(array("message"=>$message,'error'=>false), Response::HTTP_OK));
        }
        $message= $this->flash->getMessage('confirm','error', ['token'=>$token]);
        return $this->handleView($this->view(array('message'=> $message,'error'=>true), Response::HTTP_OK));
    }

    /**
     * "get_user"      [GET] /users/{username}
     *
     * @param Request $request
     *
     * @return mixed *
     * @internal param $username
     * @Route\Get( "/users")
     * @Route\View(serializerGroups={"api_user"})
     */
    public function getAction(Request $request)
    {
        $username = $request->query->get("username");
       $user = $this->userManager->findUserByUsername($username);
       if(!is_object($user) || !$user instanceof UserInterface){
         return $this->handleView($this->view($this->flash->getMessage('get', 'error',['username'=>$username]), Response:: HTTP_NOT_FOUND));
       }
       $event = new UserEvent($user,null,$request);
       $this->dispatcher->dispatch("znd_user.onUserGet",$event);
       return $this->handleView($this->view($user, Response::HTTP_OK));
    }

    /**
     * "get_user"      [GET] /users/{username}
     *
     * @param Request $request
     *
     * @return mixed *
     * @internal param $username
     * @Route\Get( "/users/by_profile")
     *           /**
     * @Route\View(serializerGroups={"api_user"})
     */
    public function getUserByProfileAction(Request $request)
    {
        $salt = $request->query->get("salt");
        $user = $this->userManager->findUserBy(array('salt'=>$salt));
        if(!is_object($user) || !$user instanceof UserInterface){
            return $this->handleView($this->view($this->flash->getMessage('get', 'error',['username'=>$salt]), Response:: HTTP_NOT_FOUND));
        }
        $event = new UserEvent($user,null,$request);
        $this->dispatcher->dispatch("znd_user.onUserGet",$event);
        return $this->handleView($this->view($user, Response::HTTP_OK));
    }

    /**
     * "get_users"     [GET] /users
     *
     * @param Request $request
     *
     * @return mixed
     * @Route\Get("/users")
     */
    public function listAction(Request $request)
    {
        $limit = 10;
        $page= $request->query->get("page");
        $offset = $this->paginator->getOffset($page, $limit);
        $users = $this->userManager->findUsers(true, $offset, $limit);
       if(!$users){
        return $this->handleView($this->view($this->flash->getMessage('list','error'), Response::HTTP_NOT_FOUND));
       }
       $page=$this->paginator->getTotalPage($this->userManager->findUsersCount(),$limit);

       return $this->handleView($this->view(["users"=>$users, "page"=>$page], Response::HTTP_OK));
    }

    /**
     * "patch_user"    [PATCH] /users
     * @param Request $request
     * @param \ZND\USM\UserBundle\Controller\username $
     *
     * @return mixed
     * @Route\Patch("/api/users/patch")
     */
    public function patchAction(Request $request)
    {   $username= $request->query->get("username");
        $user= $this->userManager->findUserByUsername($username);
        if (!$user instanceof UserInterface){
            return $this->handleView($this->view($this->flash->getMessage('test','error'), Response::HTTP_BAD_REQUEST));
        }
        $form = $this->userFormFactory->createUser($user);
        $event = new UserEvent($user, $form, $request);
        $form= $event->getForm();
        $this->dispatcher->dispatch("znd_user.onUserPatch",  $event);
        if(!$event->getStatus()){
            return $this->handleView($this->view($this->flash->getMessage('patch','error'), Response::HTTP_BAD_REQUEST));
        }
        return $this->handleView($this->view($form->getData(), Response::HTTP_OK));
    }

    /**
     * Change user password.
     *
     * @param Request $request
     *
     * @param         $username
     *
     * @return Response
     * @Route\Get("/api/users/password_change/{username}")
     */
    public function changeAction(Request $request, $username)
    {
        $user = $this->userManager->findUserByUsername($username);
        if (!is_object($user) || !$user instanceof UserInterface || $user->getUsername()!=$username) {
            return $this->handleView($this->view($username, Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $form = $this->userFormFactory->createUser($user);
        $event = new UserEvent($user,$form, $request);
        $this->dispatcher->dispatch('znd_user.onUserChange', $event);
        $code= Response::HTTP_OK;
        if ($event->getStatus()){
            $code= Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return $this->handleView($this->view($form->createView(), $code));
    }

    /**
     * "post_users"    [GET] /users   ok
     * @param Request $request
     *
     * @return mixed
     * @Route\Get("/api/users/edit")
     */
    public function editAction(Request $request)
    {   $user= $this->userManager->findUserById($request->query->get('username'));
        $form = $this->userFormFactory->createUser($user);
        $event = new UserEvent($user, $form, $request);
        $this->dispatcher->dispatch('znd_user.onUserNew', $event);
        if ($event->getStatus()==Response::HTTP_CONTINUE){
            return $this->handleView($this->view($form->all(), Response::HTTP_OK));
        }
        return $this->handleView($this->view(null,$event->getStatus()));
    }
    /**
     * "put_user"      [PUT] /users/{username}
     *
     * @param Request $request
     * @param                                           $username
     *
     * @return mixed
     * @Route\Put("/api/users")
     */
    public function putAction(Request $request, $username)
    {
        $user = $this->userManager->findUserByUsername($username);
        if((!is_object($user) || !$user instanceof UserInterface) &&$username != $user->getUsername()){
            return $this->getViewHandler()->handle($this->view($this->flash->getMessage('put','error',['username'=>$username]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        $form = $this->userFormFactory->createUser($user);

        $event = new UserEvent($form, $user, $request);
        $this->dispatcher->dispatch('znd_user.onUserPut',  $event);
        if ($form->isValid()) {
            $this->dispatcher->dispatch('znd_user.onUserPutSuccess', $event);
            if ($event->getStatus()===true){
                $this->userManager->updateUser($user);
                return $this->getViewHandler()->handle($this->view($user, Response::HTTP_CREATED));
            }
            return $this->getViewHandler()->handle($this->view($this->flash->getMessage('put','error',['username'=>$username]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
        }
        return $this->getViewHandler()->handle($this->view($this->flash->getMessage('put','error',['username'=>$username]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
    }


    /**
     * "lock_user"     [PATCH] /users/lock/{username}
     *
     * @param Request $request
     * @param                                           $username
     *
     * @return mixed
     * @Route\Lock("/api/users")
     */
    public function lockAction(Request $request, $username)
    {
      $user = $this->getUser();
      if(!is_object($user) || !$user instanceof UserInterface && $user->getUsername()!=$username){
        return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','error',['username'=>$username]), Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED));
      }
      $event = new UserEvent($user, $request);
      $this->dispatcher->dispatch("znd_user.onUserLock", $event);
      if(!$user->isLocked()){
          return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','error',['username'=>$username]), Response::HTTP_BAD_REQUEST));
      }
      $this->userManager->updateUser($user);
        return $this->getViewHandler()->handle($this->view($this->flash->getMessage('lock','success',['username'=>$username]), Response::HTTP_LOCKED));
    }

    /**
     * @param Request $request
     *
     * @internal param Request $username
     * @Route\Unlock("/api/users")
     */
    public function unlockAction(Request $request){

    }
}