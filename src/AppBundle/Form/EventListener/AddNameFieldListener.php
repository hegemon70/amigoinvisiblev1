<?php
// src/AppBundle/Form/EventListener/AddNameFieldListener.php
namespace AppBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sorteo;
use Psr\Log\LoggerInterface;

class AddNameFieldListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger=$logger;
    }

	public static function getSubscribedEvents()
    {
        return array(
            //FormEvents::POST_SET_DATA => 'onPostSetData',
             FormEvents::POST_SUBMIT   => 'onPostSubmit',
        );
    }

    public function onPostSetData(FormEvent $event)
    {
        $participante = $event->getForm()->getData();
        $form = $event->getForm();

       
    }

     public function onPostSubmit(FormEvent $event)
    {
        $participante = $event->getForm()->getData();
        $form = $event->getForm();
        $this->logger->info('hemos llegado al listener de Name');
    }
}