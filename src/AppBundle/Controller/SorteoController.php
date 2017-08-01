<?php
//AppBundle\Controller\SorteaController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Sorteo;
use AppBundle\Form\SorteoType;

class SorteoController extends Controller
{
	 /**
     * @Route("/sorteo/{id}", name="homepage_sorteo")
     */
    public function sorteoAction($id)
    {
    	$sorteo =new Sorteo();
    	$form=$this->createForm(SorteoType::class,$sorteo);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        
        return $this->render('default/Sorteo.html.twig',
                         array('form'=>$form->createView()
                         	));
       
    }
}

