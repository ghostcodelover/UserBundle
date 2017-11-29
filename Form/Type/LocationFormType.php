<?php
/**
 * Created by PhpStorm.
 * User: localgit
 * Date: 7/7/17
 * Time: 1:26 PM
 */

namespace UserBundle\Form\Type;


use ZND\SIM\ApiBundle\Form\Type\ApiFormType;
use ZND\USM\UserBundle\Form\Helper\UserFormHelper;
use Symfony\Component\Form\FormBuilderInterface;

class LocationFormType extends ApiFormType
{


    /**
     * @param string $class The User class name
     * @DI\InjectParams({
     *  "class"=@DI\Inject("%events_location.location_entity_class%")
     *})
     */
    public function __construct($class= 'UserBundle\Entity\Location')
    {
        parent::__construct($class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('country', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('state', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('town', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('commune', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('avenue', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('street', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\TextType') )
            ->add('streetNumber', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\IntegerType') )
            ->add('longitude', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\NumberType') )
            ->add('latitude', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\NumberType') )
            ->add('postal', UserFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\IntegerType'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}