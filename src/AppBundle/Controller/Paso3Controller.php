<?php
//AppBundle\Controller\Paso3Controller.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sorteo;
use AppBundle\Form\SorteoType;

class Paso3Controller extends Controller
{

	/**
     * @Route("/paso3/{id}", name="paso3")
     */
    public function envioAction(Request $request,$id)
    {

    	$logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);

        $em = $this->getDoctrine()->getManager();
        $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
        $sorteo=$sorteos_rep->findOneById($id);
        $idSorteo=$sorteo->getId();
        
        $form=$this->createForm(SorteoType::class,$sorteo);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            if($form->get('save')->isClicked())
            {
            	$helpers->enviaCorreosSorteo($sorteo);
            	return $this->redirectToRoute('sorteo_mostrar',array('id'=>$idSorteo));
            }
            else
            {
            	 return $this->redirectToRoute('homepage_sorteo',array('id'=>$idSorteo));
            }
        }

         return $this->render('default/Paso3.html.twig',
                         array( 'form'=>$form->createView()
                ));
    }
}