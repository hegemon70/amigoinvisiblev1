<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sorteo;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ParticipanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre',TextType::class,array('attr' => array('class'=>'form-control','placeholder'=>'Nombre')))
                ->add('correo',TextType::class,array('attr' => array('class'=>'form-control','placeholder'=>'email')))
                ->add('asignado',HiddenType::class)
                ->add('idSorteo',HiddenType::class)
                ->add('save', SubmitType::class, array('label'=> 'Guerdar','attr'=>array('class'=>'btn btn-default')))
                -> add('cancel', SubmitType::class, array('label'=>'Cnacelar','attr'=>array('formnovalidate'=>'formnovalidate','class'=>'btn btn-default')))
                ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Participante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_participante';
    }


}
