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

}
