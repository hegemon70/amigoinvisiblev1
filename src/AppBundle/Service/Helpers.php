<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Participante;
use Psr\Log\LoggerInterface;

class Helpers {
 
 	protected $em;
    private $container;

    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function hola(){
        return "Hola desde el servicio";
    }
     
    public function existeSesionActualGuardada()
    {
        $logger=$this->get('Logger');
        $count=-1;
        $codSesion=$this->get('session')->getId();
        $count=self::dameNumSesionesEsteId($codSesion);
         
        if ($count==1)//solo un resultado
            return true;
        else
            return false;        
    }

    private function dameNumSesionesEsteId($codSesion)
    {
        $logger=$this->get('Logger');
         $count=-1;
         try 
         {
         	//$em = $this->getDoctrine()->getManager();
        	$sesion_repo=$this->$em->getRepository("AppBundle:Sesion");
        	$sesiones= $sesion_repo->findOnecodSesion($codSesion);
        	$count=count($sesiones);
            
        } 
        catch (Exception $e) 
        {
            $logger->error('fallo al leer el num sesion actual esta guardada: '. $e->getMessage);   
        }     
        return $count;
    }
/*
    public function dameArrayParticipantesNoSorteados($idSorteoProv)
    {
         $logger=$this->get('logger');
          try {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $qb->select('p');
            $qb->from('AppBundle:Participante','p');
            $qb->where('p.sorteo = ?1');
            $qb->setParameter(1,$idSorteoProv);
            //TODO CAMBIAR NULL POR LA ID DEL SORTEO DE LA SESSION
            $idParticipantes = $qb->getQuery()->getArrayResult();
        } catch (Exception $e) {
            $idParticipantes=NULL;
            $logger.error('error en DefaultController/dameArrayParticipantesNoSorteados: '.$e->getMessage());
        }
        return $idParticipantes;
    }
*/
    public function dameArrayParticipantesNoSorteados(LoggerInterface $logger)
    {
        //$logger=$this->get('logger');
        try 
        {   
           // $em1=$this->$em->getDoctrine()->getManager();
            $participante_repo=$em1->getRepository("AppBundle:Participante");
            $participantes= $participante_repo->findAll();
            $participantesNoSorteados= new ArrayCollection();
            foreach ($participantes as $participante) 
            {
                if (is_null($participamte->getIdSorteo()))
                    $participantesNoSorteados[]=$participante;
            }
        } catch (Exception $e) {
             $idParticipantes=NULL;
            $logger.error('error en dameArrayParticipantesNoSorteados: '.$e->getMessage());
        }
    }

}   