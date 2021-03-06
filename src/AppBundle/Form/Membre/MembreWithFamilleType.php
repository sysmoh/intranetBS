<?php

namespace AppBundle\Form\Membre;

use AppBundle\Form\Famille\FamilleDisabledNomType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class MembreWithFamilleType extends MembreType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->remove('prenom')
            ->add('prenom',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Prénom',
                    'disabled' => true));

        $builder->remove('famille')
            ->add('famille', new FamilleDisabledNomType());
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Membre'
        ));
    }


    public function getBlockPrefix()
    {
        return 'appbundle_membre_add_with_famille';
    }

}
