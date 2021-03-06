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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Psr\Log\LoggerInterface;

class ParticipanteType extends AbstractType
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger=$logger;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre',TextType::class,array(
            'attr' => array(
                'class'=>'form-control col-sm-12 col-md-6',
                'placeholder'=>'Nombre',

            )))
                ->add('correo',EmailType::class,array('attr' => array('class'=>'form-control col-sm-12 col-md-6','placeholder'=>'email')))
                ->add('asignado',HiddenType::class)
                ->add('idSorteo',HiddenType::class)
                ->add('position',HiddenType::class,
                    ['attr'=>['class'=>'my-position',],
                    ])
                //->add('add', SubmitType::class, array('attr'=>array('class'=>'btn-sm btn-default','title'=>"añadir Participante")))
                ->add('save', SubmitType::class, array('label'=> 'enviar','attr'=>array('class'=>'btn btn-default')))
                //-> add('cancel', SubmitType::class, array('label'=>'Cnacelar','attr'=>array('formnovalidate'=>'formnovalidate','class'=>'btn btn-default')))
                //->addEventListener(FormEvents::POST_SET_DATA,
                //   array($this,'onPostSetData'))
                ->addEventListener(FormEvents::POST_SUBMIT,
                    array($this,'onPostSubmit'));
  
    }


    public function onPostSubmit(FormEvent $event)
    {
            $builder= $event->getForm();
            $participante=$event->getForm()->getData();
            $this->logger->info('estoy en el onPostSubmit del Participante '.$participante);
    /*
        if(null != $event->getData())
        {
            $builder= $event->getForm();
            $participante=$event->getForm()->getData();
               var_dump($participante);
        }
        */
    }

    public function onPostSetData(FormEvent $event)
    {

            $builder= $event->getForm();
            $participante=$event->getForm()->getData();
        /*
        if(null != $event->getData())
        {
            $builder= $event->getForm();
            $participante=$event->getForm()->getData();
         
        }*/
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
        return 'ParticipanteType';
        //return 'appbundle_participante';
    }


}