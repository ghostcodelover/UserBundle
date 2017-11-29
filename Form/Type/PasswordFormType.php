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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserGroupFormType
 *
 * @package ZND\USM\UserBundle\Form\Type
 */
class UserPasswordFormType extends ApiFormType
{

    /**
     * @param string $class The Group class name
     */
    public function __construct($class)
    {
        parent::__construct($class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, ['label' => 'form.group_name', 'translation_domain' => 'FOSUserBundle']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->class,
            'csrf_token_id' => 'group',
            // BC for SF < 2.8
            'intention' => 'group',
        ]);
    }

    // BC for SF < 3.0
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix().'user_group';
    }
}
