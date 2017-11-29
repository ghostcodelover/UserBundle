<?php

/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Form\Type;

use ZND\SIM\ApiBundle\Form\Type\ApiFormType;
use ZND\USM\UserBundle\Form\Helper\UserFormHelper;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserFormType
 *
 * @package ZND\USM\UserBundle\Form\Type
 *
 * @DI\Service("znd_user.user_form_type")
 */
class UserFormType extends ApiFormType
{
    /**
     * @param string $class The User class name
     * @DI\InjectParams({
     *  "class"=@DI\Inject("%znd_user.user_entity_class%")
     *})
     */
    public function __construct($class= 'ZND\USM\UserBundle\Entity\User')
    {
        parent::__construct($class);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
         $builder
             ->add('email', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'EventUserBundle'))
             ->add('username', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType'), array('label' => 'form.username', 'translation_domain' => 'EventsUserBundle'))
             ->add('first_name', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType'), array('label' => 'form.username', 'translation_domain' => 'EventsUserBundle'))
             ->add('last_name', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType'), array('label' => 'form.username', 'translation_domain' => 'EventsUserBundle'))
             ->add('plainPassword', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'))
             ->add('phone', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\NumberType'), array('label' => 'form.username', 'translation_domain' => 'EventsUserBundle'));

    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}