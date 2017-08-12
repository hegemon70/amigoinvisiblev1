<?php
//AppBundle\Controller\RecuperaController.php
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
    	$logger=$this->get('logger');
        $helpers = $this->get('app.helpers');

        $localizacion=$helpers->dameNombreActionActual($request);

        $sorteo=new Sorteo();
        $form=$this->createForm(SorteoType::class,$sorteo);

        if ($form->isSubmitted() && $form->isValid()) 
        {
          	if($form->get('save')->isClicked())
          	{

			}
			else
			{
				//$request->getSession()->set('arrPosiciones',$arrPosiciones);
                 return $this->redirectToRoute('homepage', array('devuelto' => false,'idSorteo'=>$id));			
			}
		}

    	 return $this->render('default/Recuperar.html.twig',
                         array('form'=>$form->createView()));
                      
    }
}