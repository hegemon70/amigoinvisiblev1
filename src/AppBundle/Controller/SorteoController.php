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
    public function sorteoAction(Request $request,$id)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        //recojo de la BBDD por el id
    	$em = $this->getDoctrine()->getManager();
        $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
        $sorteo=$sorteos_rep->findOneById($id);

        if (!is_null($sorteo))//si sorteo recuperado
        {
            $logger->info('sorteo: '.$sorteo.'sin asunto ni mensaje');
        }

         $arrPosiciones=$helpers->dameArrayPosiciones($sorteo);
        if(is_null($arrPosiciones))
        {
            $logger->warning('arrPosiciones esta a Null en sorteo');
        }
        /*
        else
        {
        
            $logger->info('muestro old posiciones desde sorteo antes de volver');
            //var_dump($arrPosiciones);
            
            if(!is_null($arrPosiciones['posiciones']))
            {
                $helpers->logeaUnArrayDeIntHorizontal($arrPosiciones['posiciones']);
            }
            else
            {
                $logger->warning('arrPosiciones["posiciones"] vacio');
            }
            $logger->info('muestro old indices desde sorteo');
            if(!is_null($arrPosiciones['indices']))
            {
                $helpers->logeaUnArrayDeIntHorizontal($arrPosiciones['indices']);
            }
            else
            {
                 $logger->warning('arrPosiciones["indices"] vacio');
            }
            
        }
        */


    	$form=$this->createForm(SorteoType::class,$sorteo);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            if($form->get('save')->isClicked())
            {
                $sorteo=$form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($sorteo);
                $em->flush();
                $logger->warning('Sorteo modificado');
                $idSorteo=$sorteo->getId();
            }
            else
            {
        if(is_null($arrPosiciones))
        {
            $logger->warning('arrPosiciones esta a Null en sorteo');
        }
        /*
        else
        {
        
            $logger->info('muestro old posiciones desde sorteo despues de volver');
            //var_dump($arrPosiciones);
            
            if(!is_null($arrPosiciones['posiciones']))
            {
                $helpers->logeaUnArrayDeIntHorizontal($arrPosiciones['posiciones']);
            }
            else
            {
                $logger->warning('arrPosiciones["posiciones"] vacio');
            }
            $logger->info('muestro old indices desde sorteo');
            if(!is_null($arrPosiciones['indices']))
            {
                $helpers->logeaUnArrayDeIntHorizontal($arrPosiciones['indices']);
            }
            else
            {
                 $logger->warning('arrPosiciones["indices"] vacio');
            }
            
        }
                
        */
                $request->getSession()->set('arrPosiciones',$arrPosiciones);
                 return $this->redirectToRoute('homepage', array('devuelto' => true,'idSorteo'=>$id)); 
            }
        }
        return $this->render('default/Sorteo.html.twig',
                         array('form'=>$form->createView()
                         	));
       
    }
}

