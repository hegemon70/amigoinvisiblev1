<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
         $reset=false;

         $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo
        if($reset)//para eliminar la sesion
            $this->get('session')->invalidate();

        
        $logger=$this->get('logger');
        $logger->info('intentando recoger var inicial');
        //comprobando sesion
        $inicial=$this->get('session')->get('inicial')?true:false;

        $helpers = $this->get('app.helpers');
        if (! $helpers->existeSesionActualGuardada())//es sesion nueva
        {
            
        }



        $logger->info(' '.$helpers->hola());
        $saludo=$helpers->hola();

        return $this->render('default/index.html.twig',
                        array("saludo"=>$saludo,
                        )); 
      
    }
}
