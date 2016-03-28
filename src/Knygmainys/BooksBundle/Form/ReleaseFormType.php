<?php

namespace Knygmainys\BooksBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleaseFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', null, array(
                'label' => 'ISBN',
                'label_attr' => array('class' => 'form-label')
            ))
            ->add('year', null, array(
                'label' => 'Išleidimo metai',
                'label_attr' => array('class' => 'form-label')
            ))
            ->add('publishingHouse', null, array(
                'label' => 'Leidykla',
                'label_attr' => array('class' => 'form-label')
            ))
            ->add('cover', 'file', array(
                'label' => 'Viršelis',
                'label_attr' => array('class' => 'form-label')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Knygmainys\BooksBundle\Entity\Release',
        ));
    }

    public function getBlockPrefix()
    {
        return 'knygmainys_books_release';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
