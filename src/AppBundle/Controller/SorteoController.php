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
        $codigo=$sorteo->getCodigoSorteo();

        if (!is_null($sorteo))//si sorteo recuperado
        {
            $logger->info('sorteo: '.$sorteo.'sin asunto ni mensaje');
        }

    	$form=$this->createForm(SorteoType::class,$sorteo);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            if($form->get('save')->isClicked())
            {
                $sorteo=$form->getData();
                $sorteo->setCodigoSorteo($codigo);//le planto codigo anterior
                $em = $this->getDoctrine()->getManager();
                $em->persist($sorteo);
                $em->flush();
                $logger->warning('Sorteo modificado');
                $idSorteo=$sorteo->getId();
            }
            else
            {

                 return $this->redirectToRoute('homepage', array('devuelto' => true,'idSorteo'=>$id)); 
            }
        }
        return $this->render('default/Sorteo.html.twig',
                         array('form'=>$form->createView()
                         	));
       
    }
    /**
     * @Route("/reenviar/{id}", name="sorteo_reenviar")
     */
    public function reenviarAction(Request $request,$id)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);
        $logger->warning('entramos en '.$localizacion);
        /*         
        try 
        {
          $em = $this->getDoctrine()->getManager();
          $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
          $sorteo=$sorteos_rep->find($id);
          $logger->warning('recuperado el sorteo '.$sorteo);
        } 
        catch (Exception $e) 
        {
           $logger->error('fallo al recuperar el sorteo con el id en '.$localizacion.' con el error: '.$e.getMessage());
        }
*/
           $sorteo=new Sorteo();
        $form=$this->createForm(SorteoType::class,$sorteo);
         

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) 
        {    
            $logger->warning('se submitte');
            if ($form->isValid()) {
                $logger->warning('se valida');
            }

            $sorteo = $form->getData();
            $logger->warning('hasta aqui llegamos');
            //TODO TRATAMOS LA PETICION
            if($form->get('cancel')->isClicked())
            {

                 $logger->warning('clic en boton volver'); 
                return $this->redirectToRoute('recuperar');  
            }
        }

         return $this->render('default/sorteo/reenvio.html.twig',
            array('form'=>$form->createView()));
    }
}

