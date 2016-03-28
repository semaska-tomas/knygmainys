<?php

namespace Knygmainys\BooksBundle\Form;

use Knygmainys\BooksBundle\Form\BookFormType;
use Knygmainys\BooksBundle\Form\ReleaseFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MergedBookReleaseFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('book', new BookFormType())
            ->add('release',  new ReleaseFormType())
            ->add('save', SubmitType::class, array(
                'label' => 'Pridėti knygą',
                'attr' => array('class' => 'btn btn-large btn-primary btn-block')
            ));
    }

    public function getBlockPrefix()
    {
        return 'knygmainys_books_merged_book_release';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
