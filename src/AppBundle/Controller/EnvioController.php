<?php
//src\AppBundle\Controller\EnvioController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sorteo;
use AppBundle\Form\SorteoType;


class EnvioController extends Controller
{
    /**
     * @Route("/envio/{id}", name="envio")
     */
    public function indexAction(Request $request,$id)
    {
        
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);

        $em = $this->getDoctrine()->getManager();
        $sorteos_rep=$em->getRepository("AppBundle:Sorteo");
        $sorteo=$sorteos_rep->find($id);
        $idSorteo=$sorteo->getId();

        self::enviaCorreosSorteo($sorteo);

       return $this->redirectToRoute('sorteo_mostrar',array('id'=>$idSorteo));
    }



     public function enviaCorreosSorteo($sorteo)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $resultado=0;
       
        $participantes=$sorteo->getParticipantes();
        
        
        foreach ($participantes as $participante) 
        {
            $resultado=self::enviaCorreoParticipante(
                $participante->getNombre(),
                $participante->getCorreo(),
                $participante->getAsignado(),
                $sorteo->getAsunto(),
                $sorteo->getMensaje(),
                $sorteo->getCodigoSorteo()
                );
            if ($resultado>0) {
                $logger->warning('correo enviado al participante: '.$participante->getNombre());
            }
            else
            {
                $logger->error('fallo en correo enviado al participante: '.$participante->getNombre());
            }
            
        }
    }


    public function enviaCorreoParticipante($nombre,$correo,$asignado,$asunto,$mensaje,$codigo)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $result=0;
        $enviador=Sorteo::ENVIADOR;
        $nombreAsignado=$helpers->dameNombreAsignado($asignado);
        if (!is_null($nombreAsignado)) 
        {
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
                    $mensaje->setBody( $this->renderView(
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
                 $result=$mailer->send($mensaje);
                 
              } catch (Exception $e) {
                  $logger->error('fallo al enviar'.$correo." ".$e->getMessage());
              }
                
        }
            //https://swiftmailer.symfony.com/docs/sending.html
            //Using the send() Method
        return $result;
    }

}
