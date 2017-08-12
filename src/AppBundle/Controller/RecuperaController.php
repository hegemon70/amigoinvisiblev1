<?php
//AppBundle\Controller\RecuperaController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Participante;
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
        $logger->info('entramos en '.$localizacion);

        $sorteo=new Sorteo();
        $form=$this->createForm(SorteoType::class,$sorteo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
          	if($form->get('save')->isClicked())
          	{	
          		$logger->info('clic en boton recuperar');
          		$codSorteo=$form->getData()->getCodigoSorteo();

          		$em = $this->getDoctrine()->getManager();
                $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
                $sorteo=$sorteos_rep->findByCodigoSorteo($codSorteo);
                if(is_null($sorteo))
                {
                	$strMensaje="codigo no valido o no encontrado";
                           $this->get('session')->getFlashBag()->add("mensaje",$strMensaje);
                }
                else
                {
                	$helpers->logeaUnInt(count($sorteo),"numero de sorteos recuperados: ");
                	$id=$sorteo->getId();
	                $participantes=$sorteo->getParticipantes();
					$contador=count($participantes);
	                 if ($contador>0)
	                 {
	                 	$helpers->logeaUnInt($id,"recupero el sorteo con el id: ");
	                 	return $this->redirectToRoute('homepage', array('devuelto' => false,'recuperado'=>true ,'idSorteo'=>$id));
	                 }
	                 else
	                 {
	                 	$strMensaje="codigo no valido o no encontrado";
	                           $this->get('session')->getFlashBag()->add("mensaje",$strMensaje);
	                 }
                }
                
          		
			}
			else
			{
                 $logger->info('clic en boton volver');	
                 return $this->redirectToRoute('homepage');		
			}
		}

    	 return $this->render('default/Recuperar.html.twig',
                         array('form'=>$form->createView()));
                      
    }
}