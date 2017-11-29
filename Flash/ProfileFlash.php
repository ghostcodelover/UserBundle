<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZND\USM\UserBundle\Flash;

use ZND\SIM\ApiBundle\Flash\ApiFlash;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserFlashInfo
 *
 * @package ZND\USM\UserBundle\FlashInfo
 * @DI\Service("znd_usm_user.profile_flash")
 */
class ProfileFlash extends ApiFlash  implements FlashInterface
{
    /**
     * @var array
     */
    protected static $messageIds= [
        'test'=>'profile.test',
        'new'=> 'profile.new',
        'show'=> 'profile.show',
        'post' =>'profile.post',
        'reset'=> 'profile.reset',
        'check'=> 'profile.check',
        'confirm'=> 'profile.confirm',
        'patch'=> 'profile.patch',
        'edit'=> 'profile.edit',
        'put'=> 'profile.put',
        'lock'=> 'profile.lock',
        'unlock'=> 'profile.unlock',
        'ban'=> 'profile.ban',
        'remove'=> 'profile.remove',
        'restore'=>'profile.restore',
        'delete'=> 'profile.delete',
        'link'=> 'profile.link',
        'unlink'=> 'profile.unlink'
    ];

    /**
     * @var array
     */
    protected static $domains=[
            'success'=> 'EventsUserBundleSuccess',
            'error'  => 'EventsUserBundleError',
            'default'=> 'EventsUserBundle',
    ];

    /**
     * FlashListener constructor.
     *
     * @param Session             $session
     * @param TranslatorInterface $translator
     * @DI\InjectParams({
     *     "session"=@DI\Inject("session"),
     *     "translator" = @DI\Inject("translator")
     *     })
     */
    public function __construct(Session $session, TranslatorInterface $translator)
    {
        parent::__construct($session, $translator);
        $this->setDomaine(self::$domains);
        $this->setMessageIds(self::$messageIds);
    }
}