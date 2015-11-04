<?php

namespace AppBundle\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Field\SemanticType;
use AppBundle\Field\GenreType;
use AppBundle\Field\DatePickerType;
use AppBundle\Entity\Membre;

class MembreSearchType extends AbstractType
{

    /**
     * Formulaire pour ajouter un membre, gestion automatique de la détection de famille
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom','text',array('label' => 'Prénom','required'=>false))
            ->add('nom','text',array('label' => 'Nom','required'=>false))
            ->add('fromNaissance','datepicker',array('label' => 'Naissance de','required'=>false))
            ->add('toNaissance','datepicker',array('label' => 'Naissance à','required'=>false))
            ->add('sexe','choice',
                array(
                    'label' => 'Sexe',
                    'choices'=>array(Membre::FEMME=>Membre::FEMME,Membre::HOMME=>Membre::HOMME),
                    'required'=>false))
            ->add('attribution',new AttributionSearchType())
            ;
    }

    public function configureOptions( \Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Search\MembreSearch'
        ));
    }


    public function getName()
    {
        return 'AppBundle_membre_search';
    }

}
