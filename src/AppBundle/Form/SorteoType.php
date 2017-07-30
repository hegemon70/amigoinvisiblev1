<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SorteoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       // $builder->add('codigoSorteo',TextType::class,array('attr' => array('class'=>'form-control','disabled'=>'disabled')))
        $builder->add('codigoSorteo',TextType::class,array('attr' => array('class'=>'form-control')))
                ->add('mensaje',TextareaType::class,array('attr' => array('class'=>'form-control','placeholder'=>'Escribe aqui las condiciones del sorteo, el precio maximo del regalo ,la fecha limite')))
                ->add('asunto',TextType::class,array('attr' => array('class'=>'form-control focusedInput','placeholder'=>'escribe aqui un titulo o asunto para el Sorteo del Amigo Invisible')))
                ->add('save', SubmitType::class, array('label' => 'Guardar', 'attr'=>array('class'=>'btn btn-default')))
                 -> add('cancel', SubmitType::class, array('label'=>'Cancelar','attr'=>array('formnovalidate'=>'formnovalidate','class'=>'btn btn-default')));
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
