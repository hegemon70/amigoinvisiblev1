<?php
//AppBundle\Controller\SorteaController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\ParameterBag;
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
        $logger->info('entramos en '.$localizacion);
                
        try 
        {
          $em = $this->getDoctrine()->getManager();
          $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
          $sorteo=$sorteos_rep->find($id);
          //marco la position en cada participante
          $intPos=count($sorteo->getParticipantes());
          for ($i=0; $i < $intPos ; $i++) { 
             $sorteo->getParticipantes()[$i]->setPosition($i);
          }
      
          $logger->warning('recuperado el sorteo '.$sorteo);
        } 
        catch (Exception $e) 
        {
           $logger->error('fallo al recuperar el sorteo con el id en '.$localizacion.' con el error: '.$e.getMessage());
        }

          // $sorteo=new Sorteo();
        $form=$this->createForm(SorteoType::class,$sorteo);
         

        //$form->handleRequest($request);

        //NOTE https://symfony.com/doc/current/form/direct_submit.html
        if($request->isMethod('POST'))
        {
            $logger->info('hemos pulsado un enviar '.$localizacion);
            //https://github.com/symfony/symfony/issues/1358
            //$formParticipante=$form->submit($request->request->
            //https://github.com/symfony/symfony/issues/13585
            //ZakClayton commented on 14 Apr 2015
            $cosa=$request->request->all();
            //$formParticipante=$form->submit($request->request->all());
            //$cosa=$formParticipante->getData();
             return $this->render('default/test.html.twig',array('cosa'=>$cosa));
           // die(var_dump($formParticipante));
           
            // $cosa=$formParticipante->getData();
            //  die(var_dump($cosa));
            // $logger->info($cosa->getParticipantes()[2]);
            // }
           /*
            $sorteoNew=$formParticipante->getData();
            //$sorteo = $form->getData();
            if (is_null($sorteoNew)){
                $logger->info('sorteo new es null');
            }
            else
            {
                  $logger->info($sorteoNew->getId());  
                  $logger->info(count($sorteoNew->getParticipantes()); 

            }
            $logger->info('datos devueltos: '.$sorteoNew);
            $arrPar=$sorteoNew->getParticipantes();
            $logger->info(count($arrPar));
            foreach ($sorteoNew->getParticipantes() as  $participante) 
            {
               $logger->info('participante devueltos: '.$participante);
            }
            */
            //var_dump($participante);
            /*
            $logger->warning('hasta aqui llega');
            foreach ($sorteo->getParticipantes() as $participante) {
                     $logger->info('participante:'.$participante);
                }    


            if ($form->isSubmitted() && $form->isValid()) {
                            $logger->warning('hasta aqui llega....');
                
            }
            */
           // return $this->redirectToRoute('sorteo_reenviar', array('id'=>$id));
            /*
            $strMensaje='enviado el correo para el participante '.$participante.' desde '.$localizacion.' ';
                      $logger->info($strMensaje);

            $this->get('session')->getFlashBag()->add("mensaje",$strMensaje);
*/
            //$form=$this->createForm(SorteoType::class,$sorteo);

            return $this->render('default/sorteo/reenvio.html.twig',
            array('form'=>$form->createView(),'sorteo'=>$sorteo
                ));
           
        }
        /*
        if ($form->isSubmitted()) 
        {    
            //$logger->info('se submitte');
            if ($form->isValid()) {
                $logger->info('se valida');
            }

            $sorteo = $form->getData();
          

            //TODO TRATAMOS LA PETICION
            if($form->get('cancel')->isClicked())
            {

                 $logger->info('clic en boton volver'); 
                return $this->redirectToRoute('recuperar');  
            }
            if($form->get('save')->isClicked())
            {
                 $logger->info('clic en boton enviar de un participante'); 
                 var_dump($sorteo);
            }
        }
        */

         return $this->render('default/sorteo/reenvio.html.twig',
            array('form'=>$form->createView(),'sorteo'=>$sorteo
                ));
    }
}

