<?php

namespace Knygmainys\BooksBundle\Form;

use Knygmainys\BooksBundle\Form\ReleaseFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;

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
            ->add('author', 'hidden', array(
                'label' => 'Autorius',
                'required' => false,
                'mapped' => false
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategorija',
                'label_attr' => array('class' => 'form-label'),
                'class' => 'Knygmainys\BooksBundle\Entity\Category',
                'choice_label' => 'name',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Pridėti knygą',
                'attr' => array('class' => 'btn btn-large btn-primary btn-block')
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
