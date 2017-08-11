<?php
//AppBundle\Controller\DefaultController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sesion;
use AppBundle\Entity\Sorteo;
use AppBundle\Form\ParticipanteType;
use AppBundle\Form\SorteoType;
use Psr\Log\LoggerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
        public function indexAction(Request $request)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');

        $localizacion=$helpers->dameNombreActionActual($request);

         $contador=0;
        $numPart=Participante::NUM_PART;// NUM_PART en entity       Participante 

         $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo
         $arrOldPosiciones[]=null;
         if ($devuelto)
         {

            $id=$request->query->get('idSorteo');
            if($request->getSession()->has('arrPosiciones'))
            {
                $arrOldPosiciones=$request->getSession()->get('arrPosiciones');

            }    
           
            try 
            {   
                $em = $this->getDoctrine()->getManager();
                $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
                $sorteoOld=$sorteos_rep->findOneById($id);
                $codigoOld=$sorteoOld->getCodigoSorteo();
                $numOldPart=count($sorteoOld->getParticipantes());

                $form=$this->createForm(SorteoType::class,$sorteoOld);
            } 
            catch (Exception $e) {
                 $logger->error('error al devolver sorteo en '.$localizacion.' '.$e->getMessage());
            }     
         }
         else//incial
         {
            $sorteo =new Sorteo();
            for ($i=0; $i < $numPart; $i++) 
            {
                 $participante = new Participante();
                 $sorteo->getParticipantes()->add($participante);
            } 
             $form=$this->createForm(SorteoType::class,$sorteo);
         }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO TRATAMOS LA PETICION
            if($form->get('save')->isClicked())
            {
                $logger->info('hemos clicado en crear Sorteo');
                 //unset($participantes);//elimino los datos anteriores a la recogida del formulario
                
                $sorteo = $form->getData();

                if($devuelto)
                {   
                    $logger->warning('formulario cambiado en default tras volver sorteo');
                    
                    $sorteo->setCodigoSorteo($codigoOld);//coloco al nuevo el codigo anterior
                    
                    if(!is_null($numOldPart))
                        $helpers->logeaUnInt($numOldPart,"numero de viejos participantes:");
                    else
                        $logger->warning('numOldPart vacio');

                    $numNewPart=count($sorteo->getParticipantes());
                    if(!is_null($numOldPart))
                        $helpers->logeaUnInt($numNewPart,"numero de viejos participantes:");
                    else
                        $logger->warning('numOldPart vacio');

                    if($numNewPart < $numOldPart)//si ha reducido el num de participantes
                    {
                        $logger->warning('se han reducido las posiciones');
                        $logger->info('muestro new posiciones');
                      $arrNewPosiciones=$helpers->damePosiciones($sorteo);
                      if(!is_null($arrNewPosiciones))
                      {
                        $helpers->logeaUnArrayDeIntHorizontal($arrNewPosiciones);
                        }
                        else
                        {
                            $logger->warning('arrNewPosiciones vacio');
                        }
                      $logger->info('muestro old posiciones');
                        if(!is_null($arrOldPosiciones['posiciones']))
                        {
                            $helpers->logeaUnArrayDeIntHorizontal($arrOldPosiciones['posiciones']);
                        }
                        else
                        {
                            $logger->warning('arrOldposiciones["posiciones"] vacio');
                        }
                       $logger->info('muestro old indices');
                        if(!is_null($arrOldPosiciones['indices']))
                        {
                            $helpers->logeaUnArrayDeIntHorizontal($arrOldPosiciones['indices']);
                        }
                        else
                        {
                             $logger->warning('arrOldposiciones["indices"] vacio');
                        }
                    }//fin si reducido
                    
                }
                else//inicial
                {
                     $logger->warning('formulario inicial');
                    //genero y creo el codigo de sorteo para grabar
                    $codigo=$helpers->generoNumeroSerieAleatorio();
                    $sorteo->setCodigoSorteo($codigo);
                }
                 //bucle para asignar posiciones
                 $numPos=count($sorteo->getParticipantes());
                 $format='%d';
                    $logger->info('hay '.sprintf($format,$numPos).' participantes');
                 for ($i=0; $i < $numPos ; $i++) 
                 { 
                     $sorteo->getParticipantes()[$i]->setPosition($i);
                 }
                //bucle para colocar la foreing key
                foreach ($sorteo->getParticipantes() as $participante)
                {
                    $participante->setIdSorteo($sorteo);

                    $format='%d';
                    $logger->info('posicion del participante es '.sprintf($format,$participante->getPosition()));
                }
                   try 
                   {
                    //https://knpuniversity.com/screencast/new-in-symfony3/form-updates
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($sorteo);
                        $em->flush();
                        $logger->warning('Sorteo guardado');
                        $idSorteo=$sorteo->getId();
                       
                    } 
                    catch (Exception $e) 
                    {
                        $logger->error('error en '.$localizacion.' '.$e->getMessage());
                    }
                    //if(!$devuelto)//NOTE solo para debug
                    return $this->redirectToRoute('homepage_sorteo',array('id'=>$idSorteo));

   
          
            }
        }

        
         return $this->render('default/index.html.twig',
            array( 'form'=>$form->createView(),
                        //"participantes"=>$participantes,
                        "contador"=>$contador,
                        "numpart"=>$numPart,
                        "recuperado"=>false,
                )           
                            );
      
    }



     /**
     * @Route("/nueva", name="homepage_nueva")
     */
     public function nuevaAction(Request $request)
    {
        $status=null;
        $data=null;
        $logger=$this->get('logger');
        $participante=new Participante();
        

        $form=$this->createForm(ParticipanteType::class,$participante);

            $em = $this->getDoctrine()->getManager();
            $participante_rep=$em->getRepository("AppBundle:Participante");
            $participantes=$participante_rep->findBySinSorteo();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            if($form->get('save')->isClicked())
            {
                 $participante=$form->getData();
                 $helpers = $this->get('app.helpers');
                 $exito=$helpers->guardaParticipante($participante);
                 if (is_null($exito))
                    $logger->info('Tus cambios han sido salvados!');
            }
            return $this->redirectToRoute('homepage'); 
        }


         return $this->render('default/participante/nuevo.html.twig',array( 'form'=>$form->createView(),

                    'status'=> $status
                ));
    }

       /**
     * @Route("/modificar/{id}", name="homepage_modificar")
     */
     public function modificarAction(Request $request,$id)
    {

        $logger=$this->get('logger');
        try 
        {
            $em = $this->getDoctrine()->getManager();
            $participante_rep=$em->getRepository("AppBundle:Participante");
            $participante=$participante_rep->findOneById($id);
        } 
        catch (Exception $e) 
        {
            $logger->error('fallo al recuperar un participante: '.$e->getMessage());
        }

         $form=$this->createForm(ParticipanteType::class,$participante);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            if($form->get('save')->isClicked())
            {
                try 
                {

                    $participante->setNombre($form->get("nombre")->getData());
                     $participante->setCorreo($form->get("correo")->getData());
                    $em->persist($participante);
                    $flush=$em->flush();
                    if($flush==null)
                        $logger->info('participante '.$id.' modificado');
                    else
                        $Logger->error('fallo al modificar el participante: '.$id.'');
                } 
                catch (Exception $e) 
                {
                    $Logger->error('fallo al modificar el participante: '.$id.''.$e.getMessage());
                }
            }
            return $this->redirectToRoute('homepage');
        }
         return $this->render('default/participante/modificar.html.twig',
                         array('form'=>$form->createView()));        
    }

    /**
     * @Route("/borrar/{id}", name="homepage_borrar")
     */
     public function borrarAction($id)
    {
        $logger=$this->get('Logger');
        
        // Creo un ENTITY MANAGER
        try 
        {
            $em = $this->getDoctrine()->getManager();
            $participante_rep=$em->getRepository("AppBundle:Participante");
            $participante=$participante_rep->findOneById($id);
            if (!is_null($participante))//si se ha encontrado el $id participante
            {
                $em->remove($participante);
                $flush=$em->flush();
                $logger->info('participante '.$id.' borrado');
            }
        } 
        catch (Exception $e) 
        {
            $logger->error('fallo al borrar: '.$e->getMessage());
        }
        return $this->redirectToRoute('homepage');
    }
  // public function sorteoAction(Request $request)
    // {
    //     $logger=$this->get('logger');
    //     $helpers = $this->get('app.helpers');

    //     $sorteo = new Sorteo();
    //     $codigo=$helpers->generoNumeroSerieAleatorio();
    //     $sorteo->setCodigoSorteo($codigo);
       

    //      $participante = new Participante();
    //     $sorteo->getParticipantes()->add($participante);

    //      $participante = new Participante();
    //     $sorteo->getParticipantes()->add($participante);

    //      $form=$this->createForm(SorteoType::class,$sorteo);
    //      $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         //TODO TRATAMOS LA PETICION
    //         if($form->get('save')->isClicked())
    //         {
    //             try 
    //            {
    //                 $sorteo = $form->getData();
    //                 $em = $this->getDoctrine()->getManager();
    //                 $em->persist($sorteo);
    //                 $em->flush();
    //                 $logger->warning('Sorteo guardado');
    //                 $idSorteo=$sorteo->getId();
    //             } 
    //             catch (Exception $e) 
    //             {
    //                 $logger->error('error en '.$localizacion.' '.$e->getMessage());
    //             }

    //            return $this->redirectToRoute('homepage_sorteo',array('id'=>$idSorteo));
    //         }
    //     }
    //     return $this->render('default/blocks/sorteo.html.twig',
    //         array( 'form'=>$form->createView()));
    // }
}
