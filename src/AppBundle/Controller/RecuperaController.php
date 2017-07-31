<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sorteo;
use AppBundle\Form\SorteoType;

class RecuperaController extends Controller
{
    /**
     * @Route("/recuperar", name="recuperar")
     */
    public function recuperarAction(Request $request)
    {
    	$titulo="view Recuperar";
    	 return $this->render('default/Recuperar.html.twig',
                         array('titulo'=>$titulo));
                      
    }
}