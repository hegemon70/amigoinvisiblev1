<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;

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
         	$em = $this->getDoctrine()->getManager();
        	$sesion_repo=$em->getRepository("AppBundle:Sesion");
        	$sesiones= $sesion_repo->findOnecodSesion($codSesion);
        	$count=count($sesiones);
         	/*
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $qb->select('count(sesion.id)');
            $qb->from('AppBundle:Sesion','sesion');
            $qb->where('sesion.codSesion = ?1');
            $qb->setParameter(1,$codSesion);
            $count= $qb->getQuery()->getSingleScalarResult();
            */
             
        } 
        catch (Exception $e) 
        {
            $logger->error('fallo al leer el num sesion actual esta guardada: '. $e->getMessage);   
        }     
        return $count;
    }

}   