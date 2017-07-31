<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sorteo;
use AppBundle\Form\SorteoType;

class Paso3Controller extends Controller
{

	/**
     * @Route("/envio/{id}", name="paso3")
     */
    public function envioAction(Request $request,$id)
    {

    	 $titulo="view Sorteo";

         return $this->render('default/Paso3.html.twig',
                         array('titulo'=>$titulo));
    }
}