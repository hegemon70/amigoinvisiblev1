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
              $id=$sorteos_rep->findByCodigoSorteoId($codSorteo);
              if(is_null($sorteo))
              {
                $strMensaje="codigo no valido o no encontrado";
                         $this->get('session')->getFlashBag()->add("mensaje",$strMensaje);
              }
              else
              {
                return $this->redirectToRoute('reenviar', array('idSorteo'=>$id));
              }
          }
          else
          {
             $logger->info('clic en boton volver'); 
             return $this->redirectToRoute('homepage');   
          }		
			}
			
  	 return $this->render('default/recuperar.html.twig',
                       array('form'=>$form->createView()));
                    
    }

    /**
     * @Route("/reenviar", name="reenviar")
     */
    public function reenviarAction(Request $request)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);
        $logger->info('entramos en '.$localizacion);

        $id=$request->query->get('idSorteo');

        try {
          $em = $this->getDoctrine()->getManager();
          $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
          $sorteo=$sorteos_rep->find($id);
        } 
        catch (Exception $e) 
        {
           $logger->error('fallo al recuperar el sorteo con el id en '.$localizacion.' con el error: '.$e.getMessage());
        }
        
        if(!is_null($sorteo))
        {
          $helpers->logeaUnInt(count($sorteo),"numero de sorteos recuperados: ");
          $helpers->logeaUnInt($id,"recupero el sorteo con el id: ");
          $id=$sorteo->getId();
          $participantes=$sorteo->getParticipantes();
          $contador=count($participantes);
       
           
            //return $this->redirectToRoute('reenviar', 'form'=>$form->createView());
        }
        else
        {
          $logger->error('fallo al recuperar el sorteo con el id en '.$localizacion);
        }

        return $this->render('default/rescate/reenvio.html.twig',
            array( 'participantes'=>$participantes,'sorteo'=>$sorteo
                )           
                            );

    }
}