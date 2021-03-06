<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class NumericIntervalSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lower', NumberType::class, array('required' => false, 'label' => 'De'))
            ->add('higher', NumberType::class, array('required' => false, 'label' => 'à'))
            ;

    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\NumericIntervalSearch'
        ));
    }


    public function getBlockPrefix()
    {
        return 'AppBundle_numeric_interval_search';
    }

}
