<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
         $reset=false;
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
         $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo
        $participantes=$helpers->dameArrayParticipantesNoSorteados($logger);
        if (count($participantes)==0)
        {
            for ($i=0; $i < $numPart; $i++) 
            {
                 $participante = new Participante();
                 $participantes[]=$participante;
            }  
        }
        else
        { 
            $contador=count($participantes);//participantes no vacios
            if (count($participantes) < $numPart)//hay menos de 10 
            {
                for($i=count($participantes); $i < $numPart; $i++)
                {
                    $participante = new Participante();
                     $participantes[]=$participante;
                }
            }
        }

        return $this->render('default/index.html.twig',
                        array("participantes"=>$participantes,
                                 "contador"=>$contador,
                                 "numpart"=>$numPart,
                                 "recuperado"=>false)); 
      
    }
}
