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

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use ZND\SIM\ApiBundle\EventListener\ApiEventListener;
use ZND\SIM\ApiBundle\Util\CodeGeneratorInterface;
use ZND\USM\UserBundle\Entity\UserInterface;
use ZND\USM\UserBundle\EntityManager\ProfileEntityManagerInterface;
use ZND\USM\UserBundle\Event\UserEvent;
use ZND\USM\UserBundle\Flash\FlashInterface;
use ZND\USM\UserBundle\Mailer\MailerInterface;
use ZND\USM\UserBundle\Util\CanonicalFieldsUpdater;
use ZND\USM\UserBundle\Util\PasswordUpdaterInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;


/**
  *@DI\Service("znd_user.user_event_listener")
  */
class UserEventListener extends ApiEventListener
{
    /**
     * @var MailerInterface
     * @DI\Inject("znd_user.user_mailer")
     */
    public $mailer;
    /**
     * @var CodeGeneratorInterface
     * @DI\Inject("events_api.api_util_code_generator")
     */
    public $codeGenerator;
    /**
     * @var string
     *@DI\Inject("%znd_user.firewall_name%")
     */
    public $firewallName;
    /**
     * @var FlashInterface
     * @DI\Inject("znd_user.user_flash")
     */
    public $flashInfo;
    /**
     * @var int
     *@DI\Inject("%znd_user.user.token_ttl%")
     */
    public $tokenTtl;
    /**
     * @var ProfileEntityManagerInterface
     * @DI\Inject("znd_user.profile_entity_manager")
     */
    public $profileManager;

    /**
     * @var PasswordUpdaterInterface
     * @DI\Inject("znd_user.user_util_password_updater")
     */
    public $passwordUpdater;
    /**
     * @var CanonicalFieldsUpdater
     * @DI\Inject("znd_user.user_util_canonical_fields_updater")
     */
    public $canonicalFieldsUpdater;
    
    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserNew")
     */
    public function onUserNew(UserEvent $event){
        $form= $event->getForm();
        $form->setData($event->getUser()->setEnabled(true));
        $event->setForm($form);
        $event->getUser()->setScores($this->scoresManager->createScores());
        $event->setStatus(Response::HTTP_OK);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserGet")
     */
    public function onUserGet(UserEvent $event){

    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserList")
     */
    public function onUserList(UserEvent $event)
    {
        $event->setStatus(Response::HTTP_OK);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPost")
     */
    public function onUserPost(UserEvent $event){
        $form= $this->process($event->getForm(), $event->getRequest(),true);
        $event->setForm($form);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPostSuccess")
     */
    public function onUserPostSuccess(UserEvent $event)
    {
           /** @var $user \ZND\USM\UserBundle\Entity\UserInterface */
            $user = $event->getForm()->getData(); ;
            $user->setEnabled(true);
            $user->setIsMailChecked(false);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->codeGenerator->generateToken());
            }
            if(null === $user->getConfirmationCode()){
                $user->setConfirmationCode($this->codeGenerator->generateCode());
            }
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPosted")
     */
    public function onUserPosted(UserEvent $event){
        $this->createSource($event);
        $this->addUserProfile($event);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserCheck")
     */
    public function onUserCheck(UserEvent $event){
        $event->getUser()->setIsMailChecked(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     ** @DI\Observe("znd_user.onUserConfirm")
     */
    public function onUserConfirm(UserEvent $event){
        $user = $event->getUser();
        $user->setIsConfirmed(true);
        $user->setConfirmationToken(null);
        $user->setConfirmationCode(null);
        $user->setEnabled(true);
        $user->addRole('ROLE_USER');
        $event->setStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPatch")
     */
    public function onUserPatch(UserEvent $event){
        $event->setForm($this->process($event->getForm(), $event->getRequest()));
        $event->setStatus(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPut")
     */
    public function onUserPut(UserEvent $event){
        $form = $this->process($event->getForm()->setData($event->getUser()), $event->getRequest());
        $event->setForm($form);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserPutSuccess")
     */
    public function onUserPutSuccess(UserEvent $event){
        $this->flashInfo->addMessage('put','success',['username'=>$event->getUser()->getUsername()]);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserActivate")
     */
    public function onUserActivate(UserEvent $event){
        $event->getUser()->setEnabled(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserActivated")
     */
    public function onUserActivated(UserEvent $event){
        $event->getUser()->setIsActivated(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserDeActivate")
     */
    public function onUserDeActivate(UserEvent $event){
        $event->getUser()->setEnabled(false);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserActivated")
     */
    public function onUserDeActivated(UserEvent $event){
        $event->getUser()->setIsActivated(false);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.eventsUserRemove")
     */
    public function onUserRemove(UserEvent $event){
        $event->getUser()->setIsRemoved(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.events_user_restore")
     */
    public function onUserRestore(UserEvent $event){
        $event->getUser()->setIsRemoved(false);

    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserLock")
     */
    public function onUserLock(UserEvent $event)
    {
        $event->getUser()->setIsLocked(true);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserUnlock")
     */
    public function onUserUnLock(UserEvent $event)
    {
        $event->getUser()->setIsLocked(false);
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserDelete")
     */
    public function onUserDelete(UserEvent $event){
        $user = $event->getUser();
        if (is_object($user)){
            $profile = $this->profileManager->findProfileById($user->getUsername());
             if (is_object($profile)){
                 $this->profileManager->deleteProfile($profile);
             }
        }
    }
    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.onUserDeleted")
     */
    public function onUserDeleted(UserEvent $event){

    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.events_user_change")
     */
    public function onUserChange(UserEvent $event)
    {
        $event->getForm()->setData($event->getUser());
        $event->setStatus(true);
    }

    /**
     * @param UserEvent $event
     * @DI\Observe("znd_user.events_user_send")
     */
    public function onEventSend(UserEvent $event)
    {
        if (!$event->getUser()->isPasswordRequestNonExpired($this->tokenTtl)) {
            $event->setStatus(false);
        }
    }

    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     * @DI\Observe("znd_user.events_user_edit")
     */
    public function onUserEdit(UserEvent $event)
    {

    }

    /**
     * Pre persist listener based on doctrine common.
     *
     * @param LifecycleEventArgs $args
     * @DI\Observe("prePersist")
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        if ($user instanceof UserInterface) {
            $this->updateUserFields($user);
        }
    }

    /**
     * Pre update listener based on doctrine common.
     *
     * @param LifecycleEventArgs $args
     * @DI\Observe("preUpdate")
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        if ($user instanceof UserInterface) {
            $this->updateUserFields($user);
            $this->recomputeChangeSet($args->getObjectManager(), $user);
        }
    }

    /**
     * Updates the user properties.
     *
     * @param UserInterface $user
     */
    private function updateUserFields(UserInterface $user)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($user);
        $this->passwordUpdater->hashPassword($user);
    }

    /**
     * Recomputes change set for Doctrine implementations not doing it automatically after the event.
     *
     * @param ObjectManager $om
     * @param UserInterface $user
     */
    private function recomputeChangeSet(ObjectManager $om, UserInterface $user)
    {
        $meta = $om->getClassMetadata(get_class($user));
        if ($om instanceof EntityManager) {
            $om->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $user);
            return;
        }
    }
    /**
     * @param \ZND\USM\UserBundle\Event\UserEvent $event
     */
    public function addUserProfile(UserEvent $event){
        $user = $event->getUser();
        $profile = $this->profileManager->createProfile();
        $profile->setUser($user);
        $position = $this->profileManager->getPosition($user->getFirstName(), $user->getLastName());
        $profile->setPosition($position);
        $profile->setFirstName($user->getFirstName());
        $profile->setLastName($user->getLastName());
        $this->profileManager->updateProfile($profile);
    }
}
