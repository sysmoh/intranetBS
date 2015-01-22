<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use AppBundle\Form\ContactType;
use AppBundle\Form\AdresseType;

class GeniteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', 'text', array('required' => false, 'label' => 'Prénom'))
            ->add('profession', 'text', array('required' => false, 'label' => 'Profession'))
            ->add('contact', new ContactType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Geniteur'
        ));
    }

    public function getName()
    {
        return 'appbundle_geniteurtype';
    }
}
