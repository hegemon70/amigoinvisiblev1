<?php
// src/AppBundle/Form/EventListener/AddEmailFieldListener.php
namespace AppBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sorteo;
use Psr\Log\LoggerInterface;

class AddEmailFieldListener implements EventSubscriberInterface
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
        $sorteo = $event->getForm()->getData();
        $form = $event->getForm();

    }

    public function onPostSubmit(FormEvent $event)
    {
        $sorteo = $event->getForm()->getData();
        $form = $event->getForm();
        if (is_null($sorteo))
            $this->logger->info('en listener de email $sorteo esta vacio');
        else{
            $this->logger->info('en listener de email $sorteo contiene algo ');
            $this->logger->info($sorteo);
            $formato="%.0f";
            $c=count($sorteo->getParticipantes());
            $this->logger->warning(sprintf($formato,$c));
            foreach ($sorteo->getParticipantes() as $value) {
               $this->logger->warning($value);
            }
        }
    }
}