<?php

namespace Knygmainys\BooksBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search_text', 'text', [
                'required' => false,
                'constraints' => new Length(['min' => 5, 'minMessage' => 'Paieškos tekstas per trumpas'])
            ])
            ->add('category', 'entity', [
                'class' => 'Knygmainys\BooksBundle\Entity\Category',
                'choice_label' => 'name',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
            ->add('type', 'choice', array(
                'choices' => array(
                    'all' => 'Visos',
                    'wanted' => 'Norimos',
                    'owned' => 'Turimos'
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'data' => 'all'
            ))
            ->add('search', 'submit', ['label' => 'Ieškoti'])
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

    public function getName()
    {
        return 'basket_planner_filter_match';
    }
}