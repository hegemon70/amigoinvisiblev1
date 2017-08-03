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

        $sorteo =new Sorteo();
        $codigo=$helpers->generoNumeroSerieAleatorio();
        $sorteo->setCodigoSorteo($codigo);
        //$arrColParticipantes=new ArrayCollection(); 
        $form=$this->createForm(SorteoType::class,$sorteo);
         //https://github.com/symfony/symfony-docs/issues/6056 HeahDude commented on 15 May 2016

        //$form = $this->createFormBuilder(SorteoType::class)
        //        ->add('save', SubmitType::class)
        //       ->getForm();
        $devuelto=$request->query->get('devuelto');//en caso de volver pag sorteo

        $contador=0;
        $numPart=Participante::NUM_PART;// NUM_PART en entity Participante
        
        //CONSULTO LOS PARTICIPANTES VACIOS
        $em = $this->getDoctrine()->getManager();
        $participante_rep=$em->getRepository("AppBundle:Participante");
        $participantes=$participante_rep->findBySinSorteo();
        
        foreach ($participantes as $participante) {
             $logger->info('el tipo de $participante es un : '.gettype($participante));
              $logger->info(' y tiene de nombre: '.$participante->getNombre());
        }
      

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
            if ($contador < $numPart)//hay menos de NUM_PART 
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


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO TRATAMOS LA PETICION
            if($form->get('save')->isClicked())
            {
                $logger->info('hemos clicado en crear Sorteo');
                $participantes=$form->get('participantes')->getData();
                $sorteo = $form->getData();
                //$participantes=$sorteo->getParticipantes();
               // var_dump($sorteo);
                $intPart=count($participantes);
                $logger->info('hay '.$intPart.' participantes');
                if($intPart<3)
                {
                    $strMin3 = 'Debes de crear al menos 3 participantes';
                    //$this->get('session')->getFlashBag()->add("min3",$strMin3);
                   $this->addFlash("min3",$strMin3);
                }
                else
                {
                   try 
                   {
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($sorteo);
                        $em->flush();
                        $logger->warning('Sorteo guardado');
                    } 
                    catch (Exception $e) 
                    {
                        $logger->error('error en '.$localizacion.' '.$e->getMessage());
                    }

                    return $this->redirectToRoute('homepage_sorteo',array('id'=>$sorteo->getId()));

                }
          
            }
        }

        
         return $this->render('default/index.html.twig',
            array( 'form'=>$form->createView(),
                        "participantes"=>$participantes,
                        "contador"=>$contador,
                        "numpart"=>$numPart,
                        "recuperado"=>false,
                        "codigo"=>$codigo
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

}
