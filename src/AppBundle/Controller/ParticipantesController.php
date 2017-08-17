<?php
//AppBundle\Controller\ParticipantesController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sesion;
use AppBundle\Entity\Sorteo;
use AppBundle\Form\ParticipanteType;
use Psr\Log\LoggerInterface;

class ParticipantesController extends Controller
{
    ///recuperamos constante de default $max = Participante::NUM_PART

     /**
     * @Route("/reenvioparticipante", name="participante_reenvio")
     */
    public function reenvioAction(Request $request,Participante $participante,Sorteo $sorteo)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');
        $localizacion=$helpers->dameNombreActionActual($request);

        $logger->info('mostrando participante desde '.$localizacion);

        $form=$this->createForm(ParticipanteType::class,$participante);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $logger->info('valida en '.$localizacion);
              if($form->get('save')->isClicked())
              {
                $logger->info('detecta click en save en '.$localizacion);
                $participante=$form->getData();
                $participante->setPosition(-1);
              }
        }
        /*
        else
        {
            $logger->error('no valida en '.$localizacion);
        }*/
/*
        if($request->isMethod('POST'))
        {
            $logger->info('hemos pulsado un enviar '.$localizacion);
        }
*/
           //https://stackoverflow.com/questions/42600672/symfony3-embedded-controller-with-form
      // $request = $this->get('request_stack')->getMasterRequest();  //recupero la peticion padre  
       // $form->handleRequest($request);

       // var_dump($request);
        /*
       if ($form->isSubmitted()) 
       {
            $logger->info('pulsado el enviar desde '.$localizacion.'');

            $participante = $form->getData();

            var_dump($participante->getCorreo());
*/
            // TRATAMOS LA PETICION
           // if($form->get('save')->isClicked())
           // {
                //is possible submit "embedded controllers" symfony
                //$logger->info('pulsado el enviar desde '.$localizacion.' valido');
                //$sorteo=$request->get('sorteo');
           //    $algo = $form->getData();//solo recibe el correo en este caso
            //   var_dump($algo);
                //$participante->setCorreo($correo);
                //enviaCorreoParticipante($nombre,$correo,$asignado,$asunto,$mensaje)
                // $resultado=$helpers->enviaCorreoParticipante(
                //     $participante->getNombre(),
                //     $participante->getCorreo(),
                //     $participante->getAsignado(),
                //     $sorteo->getAsunto(),
                //     $sorteo->getMensaje()
                // );

                // if ($resultado > 0) //0 fallo de envio
                // {
                //     $strMensaje='enviado el correo para el participante '.$participante.' desde '.$localizacion.' ';
                //      $logger->info($strMensaje);

                //       $strMensaje="enviado";
                //          $this->get('enviado')->getFlashBag()->add("mensaje",$strMensaje);
                // }
                // else
                // {
                //      $strMensaje='fallo en envio del correo para el participante '.$participante.' desde '.$localizacion.' ';
                //     $logger->error($strMensaje);

                //          $this->get('no_enviado')->getFlashBag()->add("mensaje",$strMensaje);
                // }
               
                //TODO SALVAR EL CAMBIO EN PARTICIPANTE
                /*
                $em = $this->getDoctrine()->getManager();
                        $em->persist($participante);
                        $em->flush();
                        $logger->warning('Participante guardado desde '.$localizacion);*/

                // return $this->render('default/index.html.twig');
            //}
       // }//end form submmited
        /*
        else
        {
            if($form->isSubmitted()){
                $logger->warning('entra en enviado');
            }
            else{
                $logger->error('NO entra en enviado');
            }       
        }
        */

        return $this->render('default/participante/reenviop.html.twig',array('form'=>$form->createView()));


    }
    /**
     * @Route("/listar", name="participantes_listar")
     */
     public function listarAction(Request $request)
    {
        $logger=$this->get('logger');
        $helpers = $this->get('app.helpers');

        $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo

        $contador=0;
        $numPart=Participante::NUM_PART;
        $em = $this->getDoctrine()->getManager();
        $participante_rep=$em->getRepository("AppBundle:Participante");
        $participantes=$participante_rep->findBySinSorteo();
        if (count($participantes)==0)
        {
            for ($i=0; $i < $numPart; $i++) 
            {
                 $participante = new Participante();
                 $participantes[]=$participante;
            }  
        }
        else
        { 
            $contador=count($participantes);//participantes no vacios
            if ($contador < $numPart)//hay menos de 10 
            {
                for($i=$contador; $i < $numPart; $i++)
                {
                    $participante = new Participante();
                     $participantes[]=$participante;
                }
            }
        }

        $formato='el num de participantes no vacios es [contador]: %.0f';
        $logger->info(sprintf($formato,$contador));

        return $this->render('default/participante/lista.html.twig',
            array(  "participantes"=>$participantes,
                        "contador"=>$contador,
                        "numpart"=>$numPart,
                        "recuperado"=>false
                )           
                            );
    }




    /**
     * @Route("/nueva", name="participantes_nueva")
     */
    public function nuevaAction(Participante $participante)
    {
    	$status=null;
        $data=null;
        $logger=$this->get('logger');
        
       // $sinDatos=(is_null($participante));//si no tiene nombre
        //if($sinDatos)//si participante vacio
        //    $participante=new Participante();
        $form=$this->createForm(ParticipanteType::class,$participante);
           /*
            if(!$sinDatos)//con datos
            {
                $em = $this->getDoctrine()->getManager();
                $participante_rep=$em->getRepository("AppBundle:Participante");
                $participantes=$participante_rep->findById($participante.getId());
            }*/
        /*
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
*/

         return $this->render('default/participante/nuevo.html.twig',array( 'form'=>$form->createView(),

                    'status'=> $status
                ));
    }

}
