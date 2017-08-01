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

        $titulo="view Sorteo";
        return $this->render('default/Sorteo.html.twig',
                         array('titulo'=>$titulo));
       
    }
}

