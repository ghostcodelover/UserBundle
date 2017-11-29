<?php


namespace ZND\USM\UserBundle\Form\Factory;

use ZND\SIM\ApiBundle\Form\Factory\ApiFormFactory;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class UserFormFactory
 *
 * @package ZND\USM\OauthBundle\Form\Factory
 * @DI\Service("znd_user.user_form_factory")
 */
class UserFormFactory extends ApiFormFactory  implements UserFormFactoryInterface
{

    private static $config= array(
        "user_form" => array(
            'type'=>'ZND\USM\UserBundle\Form\Type\UserFormType',
            'name'=> 'events_user_user_form'
        ),
        "profile_form"=> array(
            'type'=> 'ZND\USM\UserBundle\Form\Type\ProfileFormType',
            'name'=> 'events_profile_form'
        )
    );

    /**
     * OauthFormFactory constructor.
     * @DI\InjectParams({
     *  "formFactory"= @DI\Inject("form.factory")
     *     })
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        parent::__construct($formFactory);
    }

    /**
     * @param null  $data
     * @param array $options
     *
     * @return bool|FormInterface
     */
    public function createUser($data = null, array $options = array())
    {
        if (!isset(self::$config['user_form']['type'],self::$config['user_form']['type'] )){
            return false;
        }
        $type = self::$config['user_form']['type'];
        $name = self::$config['user_form']['name'];
        return $this->createForm($name, $type,$data, $options);
    }

    /**
     * @param null  $data
     * @param array $options
     *
     * @return bool| FormInterface
     */
    public function createProfile($data = null, array $options = array())
    {
        if (!isset(self::$config['profile_form']['type'],self::$config['profile_form']['type'] )){
            return false;
        }
        $type = self::$config['profile_form']['type'];
        $name = self::$config['profile_form']['name'];
        return $this->createForm($name, $type,$data, $options);
    }

    /**
     * @param null  $data
     * @param array $options
     *
     * @return bool|FormInterface
     */
    public function createLocation($data = null, array $options = array())
    {
        if (!isset(self::$config['location_form']['type'],self::$config['location_form']['type'] )){
            return false;
        }
        $type = self::$config['location_form']['type'];
        $name = self::$config['location_form']['name'];
        return $this->createForm($name, $type,$data, $options);
    }
}