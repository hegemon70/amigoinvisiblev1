<?php
// src/AppBundle/Form/EventListener/AddEmailFieldListener.php
namespace AppBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sorteo;

class AddEmailFieldListener implements EventSubscriberInterface
{

	public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'onPostSetData',
            //FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPostSetData(FormEvent $event)
    {
        $participante = $event->getForm()->getData();
        //$participante = $event->getData();
        $form = $event->getForm();
    }
}