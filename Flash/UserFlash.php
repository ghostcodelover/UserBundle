<?php


namespace ZND\USM\UserBundle\Flash;

use ZND\SIM\ApiBundle\Flash\ApiFlash;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserFlashInfo
 *
 * @package ZND\USM\UserBundle\FlashInfo
 * @DI\Service("znd_usm_user.user_flash")
 */
class UserFlash extends ApiFlash  implements FlashInterface
{
    /**
     * @var array
     */
    protected static $messageIds= [
        'test'=>'user.test',
        'new'=> 'user.new',
        'list' => 'user.list',
        'get'=> 'user.get',
        'post' =>'user.post',
        'reset'=> 'user.reset',
        'check'=> 'user.check',
        'confirm'=> 'user.confirm',
        'patch'=> 'user.patch',
        'edit'=> 'user.edit',
        'put'=> 'user.put',
        'lock'=> 'user.lock',
        'unlock'=> 'user.unlock',
        'ban'=> 'user.ban',
        'remove'=> 'user.remove',
        'restore'=>'user.restore',
        'delete'=> 'user.delete',
        'link'=> 'user.link',
        'unlink'=> 'user.unlink'
    ];

    /**
     * @var array
     */
    protected static $domains=[
            'success'=> 'EventsUserBundleSuccess',
            'error'  => 'EventsUserBundleError',
            'admin'  => 'EventsAdminBundle',
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
        $this->setDomain(self::$domains);
        $this->setMessageIds(self::$messageIds);
    }
}