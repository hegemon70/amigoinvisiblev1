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
         $reducido=false;
         $recuperado=false;
         $devuelto=false;
         $numReducidos=0;
        $numPart=Participante::NUM_PART;// NUM_PART en entity       Participante 

         $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo
         $recuperado=$request->query->get('recuperado');//en caso de volver pag recuperado
        // $arrOldPosiciones[]=null;
         if ($devuelto Or $recuperado)
         {

            $id=$request->query->get('idSorteo');     
            try 
            {   
                $em = $this->getDoctrine()->getManager();
                $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
                $sorteoOld=$sorteos_rep->findOneById($id);
                $codigoOld=$sorteoOld->getCodigoSorteo();
                if ($recuperado) 
                {
                   $asuntoOld=$sorteoOld->getAsunto();
                   $mensajeOld=$sorteoOld->getMensaje();
                }
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

                if($devuelto Or $recuperado)
                {   
                    if ($devuelto) {
                        $logger->warning('formulario cambiado en default tras volver de sorteo');
                    }
                    else
                    {
                       $logger->warning('formulario cambiado en default tras volver de recuperado'); 
                    }
                   
                    
                    $sorteo->setCodigoSorteo($codigoOld);//coloco al nuevo el codigo anterior
                    if($recuperado)
                    {
                        $sorteo->setAsunto($asuntoOld);
                        $sorteo->setMensaje($mensajeOld);
                    }
                                                      
                    $numNewPart=count($sorteo->getParticipantes());
                   
                    if($numNewPart < $numOldPart)//si ha reducido el num de participantes
                    {
                        $logger->warning('se han reducido las posiciones');
                       $reducido=true;
                       $numReducidos=$numOldPart-$numNewPart;

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
                    if($reducido)
                    {
                        $em = $this->getDoctrine()->getManager();
                            $participantes_rep=$em->getRepository("AppBundle:Participante");
                        if ($numReducidos==1) 
                        {
                            $partEliminado=null;
                            
                            $partEliminado=$participantes_rep->findByParticipantesSorteoMaxId($id);
                            if (!is_null($partEliminado)) 
                            {
                                $em = $this->getDoctrine()->getManager();
                                $em->remove($partEliminado);
                                $em->flush();
                                $logger->info('eliminado el participante');
                            }
                            else{
                             $logger->error('fallo al recuperar el reducido en '.$localizacion);
                            }
                            
                        }
                        else //mas de 1 reducidos
                        {

                            if ($numReducidos>1) 
                            {
                                //recupero todos los participantes eliminados
                               $partEliminados=$participantes_rep->findByParticipantesSorteoModernos($id,$numReducidos);
                            }
                            else{
                                $logger->error('fallo al contar los reducidos en '.$localizacion);
                            }
                            $em = $this->getDoctrine()->getManager();
                            foreach ($partEliminados as $partEliminado) 
                            {
                                if (!is_null($partEliminado)) 
                                {
                                    $em = $this->getDoctrine()->getManager();
                                    $em->remove($partEliminado);
                                    $em->flush();
                                    $logger->info('eliminado el participante');
                                }
                                else
                                {
                                    $logger->error('fallo al recuparar los reducidos en '.$localizacion);
                                }
                            }
                        }//fin mas de 1 reducido
                        
                    }//fin reducido

                    return $this->redirectToRoute('homepage_sorteo',array('id'=>$idSorteo));
                }//fin clic on save
        }//fin form valido

        
         return $this->render('default/index.html.twig',
            array( 'form'=>$form->createView(),
                        //"participantes"=>$participantes,
                        "contador"=>$contador,
                        "numpart"=>$numPart,
                        "recuperado"=>$recuperado,
                )           
                            );
      
    }//fin indexAction





   
   
}
