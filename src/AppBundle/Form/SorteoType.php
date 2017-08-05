<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Form\ParticipanteType;

class SorteoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigoSorteo',TextType::class,array('attr' => array('class'=>'form-control')))
        ->add('participantes', CollectionType::class,
                    array(
                        'entry_type'=>ParticipanteType::class,
                        'by_reference'=>false
                    ))
        ->add('mensaje',HiddenType::class)
        ->add('asunto',HiddenType::class)
        ->add('save', SubmitType::class, array('label' => 'Guardar', 'attr'=>array('class'=>'btn btn-default')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sorteo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_sorteo';
    }


}
