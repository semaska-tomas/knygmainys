<?php

namespace Knygmainys\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => 'Vardas'))
            ->add('lastName', 'text', array('label' => 'Pavardė'))
            ->add('city', EntityType::class, array(
                'label' => 'Miestas',
                'class' => 'Knygmainys\UserBundle\Entity\City',
                'choice_label' => 'name',
            ))
            ->add('address', 'text', array('label' => 'Adresas'))
            ->add('postCode', 'text', array('label' => 'Pašto kodas'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Knygmainys\UserBundle\Entity\User',
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'knygmainys_user_registration';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}