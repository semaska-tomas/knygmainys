<?php

namespace Knygmainys\BooksBundle\Form;

use Knygmainys\BooksBundle\Form\ReleaseFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'label' => 'Knygos Pavadinimas',
                'label_attr' => array('class' => 'form-label')
            ))
            ->add('description', 'textarea', array(
                'label' => 'Aprašymas',
                'label_attr' => array('class' => 'form-label')
            ))
            ->add('author', 'entity', array(
                'label' => 'Autorius',
                'label_attr' => array('class' => 'form-label'),
                'class' => 'Knygmainys\BooksBundle\Entity\Author',
                'property'     => 'fullName',
                'multiple'     => true
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategorija',
                'label_attr' => array('class' => 'form-label'),
                'class' => 'Knygmainys\BooksBundle\Entity\Category',
                'choice_label' => 'name',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Knygmainys\BooksBundle\Entity\Book',
        ));
    }

    public function getBlockPrefix()
    {
        return 'knygmainys_books_book';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
