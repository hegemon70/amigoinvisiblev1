<?php
//AppBundle\Controller\SorteaController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
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
        $helpers->gestionaParticipantes($sorteo);

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
                return $this->redirectToRoute('paso3',array('id'=>$idSorteo));
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

        $form=$this->createForm(SorteoType::class,$sorteo);
         
        $form->handleRequest($request);
        if($form->get('cancel')->isClicked())
        {
            $logger->info('clic en boton volver en '.$localizacion);return $this->redirectToRoute('homepage'); 
        }

        //NOTE https://symfony.com/doc/current/form/direct_submit.html
        if($request->isMethod('POST'))
        {
            $logger->info('hemos pulsado un boton en '.$localizacion);
           
            //NOTE https://github.com/symfony/symfony/issues/13585
            //ZakClayton commented on 14 Apr 2015
            $participanteModif=$request->request->all();
            //busco el participante modificado y el coloco el correo nuevo
            $intPosModif=$participanteModif['ParticipanteType']['position'];
            foreach ($sorteo->getParticipantes() as $participante) 
            {
                $intPos=$participante->getPosition();
                if ($intPosModif==$intPos)
                {
                    $correo=$participanteModif['ParticipanteType']['correo'];
                    $participante->setCorreo($correo);
                    /*
                    //TODO enviamos correo
                   $resultado=$helpers->enviaCorreoParticipante(
                        $participante->getNombre(),
                        $participante->getCorreo(),
                        $participante->getAsignado(),
                        $sorteo->getAsunto(),
                        $sorteo->getMensaje(),
                        $sorteo->getCodigoSorteo()
                    );
                   //https://swiftmailer.symfony.com/docs/sending.html
                   //Using the send() Method
                   */
                                  $resultado=0;
                    $enviador=Sorteo::ENVIADOR;
                    $asignado = $participante->getAsignado();
                    $nombre = $participante->getNombre();
                    $correo = $participante->getCorreo();
                    $nombreAsignado=$helpers->dameNombreAsignado($asignado);
                    $asunto = $sorteo->getAsunto();
                    $mensaje= $sorteo->getMensaje();
                    $codigo = $sorteo->getCodigoSorteo();
                     //$transporter = new \Swift_SmtpTransport('smtp-relay.gmail.com');
        $transporter = new \Swift_SmtpTransport('aspmx.l.google.com');
        //$transporter = new \Swift_SmtpTransport('smtp.gmail.com');
        $mailer = new \Swift_Mailer($transporter);
          try {
                $mensaje = (new \Swift_Message($asunto));
                $mensaje->setFrom($enviador);
                $mensaje->setTo($correo);
                //https://stackoverflow.com/questions/9143993/swiftmailerbundle-how-can-send-email-with-html-content-symfony-2
                $mensaje->setContentType("text/html");
                $mensaje->setBody(
                      $this->renderView(
                        'default/Email.html.twig',
                        array(  'asunto' => $asunto,
                                'mensaje'=> $mensaje,
                                'asignado'=> $nombreAsignado,
                                'codigo'=> $codigo,
                                'nombre'=> $nombre,
                                'showHead'=>false
                            ),
                        'text/html')
                  );
             $resultado=$mailer->send($mensaje);
             
          } catch (Exception $e) {
              $this->logger->error('fallo al enviar'.$correo." ".$e->getMessage());
          }
                   if ($resultado>0)
                   {
                     //TODO SALVAR EL CAMBIO EN PARTICIPANTE
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($participante);
                        $em->flush();
                        $logger->warning('Participante guardado desde '.$localizacion);
                       $strMensaje='enviado el correo para el participante '.$participante;
                       $this->addFlash('exito',$strMensaje);
                       $strMensaje.=$strMensaje.' desde '.$localizacion.' ';
                       $logger->info($strMensaje);                    
                   }
                   else
                   {
                         $strMensaje='fallo al enviar el correo para el participante '.$participante.' desde '.$localizacion.' ';
                         
                         $this->addFlash('fallo',$strMensaje);
                         $strMensaje.=$strMensaje.' desde '.$localizacion.' ';
                         $logger->error($strMensaje);
                   }
                   
                   break;//salgo del bucle
                }//fin $intPosModif==$intPos
                 
            }//fin foreach
            
            return $this->render('default/sorteo/reenvio.html.twig',
            array('form'=>$form->createView(),'sorteo'=>$sorteo
                ));
           
        }//fin POST

         return $this->render('default/sorteo/reenvio.html.twig',
            array('form'=>$form->createView(),'sorteo'=>$sorteo
                ));
    }

    /**
     * @Route("/mostrar/{id}", name="sorteo_mostrar")
     */
    public function mostrarAction(Request $request,$id)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);
        $logger->info('entramos en '.$localizacion);

        $em = $this->getDoctrine()->getManager();
        $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
        $sorteo=$sorteos_rep->findOneById($id);

        $form=$this->createForm(SorteoType::class,$sorteo);


        return $this->render('default/sorteo/mostrar.html.twig',
            array('form'=>$form->createView(),
                'sorteo'=>$sorteo,
                'id'=>$id
                ));
    }

    /**
     * @Route("/veremail/{id}", name="sorteo_email")
     */
    public function emailAction(Request $request,$id)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);
        $logger->info('entramos en '.$localizacion);

        $em = $this->getDoctrine()->getManager();
        $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
        $sorteo=$sorteos_rep->findOneById($id);
        $participantes=$sorteo->getParticipantes();
        $longPart=count($participantes);
        $nombreAsignado=null;
        if ($longPart>2) 
        {
            $participante=$participantes[0];
            $nombre=$participante->getNombre();
            // $idPar=$participante->getId();
            // $helpers->logeaUnInt($idPar,"este el id del participante sondeado: ");
            $asignado=$participante->getAsignado();
            // $helpers->logeaUnInt($asignado,"este el id del participante asignado: ");
            $nombreAsignado=$helpers->dameNombreAsignado($asignado);
        }
        $asunto=$sorteo->getAsunto();
        $mensaje=$sorteo->getMensaje();
         $codigo=$sorteo->getCodigoSorteo();
        
         return $this->render('default/email.html.twig',
            array('asunto'=>$asunto,
                'mensaje'=>$mensaje,
                'asignado'=>$nombreAsignado,
                'codigo'=>$codigo,
                'nombre'=>$nombre,
                'showHead'=>true
                ));
    }
}

