<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Participante;
use AppBundle\Entity\Sorteo;
use Psr\Log\LoggerInterface;

class Helpers {
 
 	protected $em;
    private $container;
    private $logger;

    public function __construct(EntityManager $entityManager, Container $container,LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->container = $container;
        $this->logger=$logger;
    }

    public function hola(){
        return "Hola desde el servicio";
    }

    public function gestionaParticipantes(Sorteo $sorteo)
    {
        $sorteado=false;
        $arrIdParticipantesOrig=self::dameArrayIdsParticipantes($sorteo);
        if (count($arrIdParticipantesOrig)>2) {
            self::logeaUnInt(count($arrIdParticipantesOrig),"numero de ids a repartir");
            $arrIdSorteados=self::creaReparto($arrIdParticipantesOrig);
            $sorteado=self::actualizaParticipantes($sorteo,$arrIdParticipantesOrig,$arrIdSorteados);
        }
        else
        {
            self::logeaUnInt(count($arrIdParticipantesOrig),"numero de ids :");
            $this->logger->error('numero de ids no valido');
        }
        
        return $sorteado; 
    }

    public function dameArrayIdsParticipantes(Sorteo $sorteo)
    {
        $arrIdsParticipantes=array();
        self::logeaUnInt(count($sorteo->getParticipantes()),"en dameArrayIdsParticipantes num participantes: ");
        foreach ($sorteo->getParticipantes() as $participante) 
        {
            $this->logger->info('construyendo el array de ids participantes ...');
            self::logeaUnInt($participante->getId(),"id a colocar :");
            $arrIdsParticipantes[]=$participante->getId();
        }
        return $arrIdsParticipantes;
    }

    public function actualizaParticipantes(Sorteo $sorteo,$arrOrig,$arrReparto)
    {
        $exito=true;
        try 
        {
            for ($i=0; $i < count($arrOrig); $i++)  
            {
                $sorteo->getParticipantes()[$i]->setAsignado($arrReparto[$i]);
            }
            $this->em->persist($sorteo);
            $this->em->flush();
        } 
        catch (Exception $e) 
        {
            $this->logger->error('fallo en actualizaParticipantes '.$e->getMessage());
            $exito=false;
        }
        return $exito;
    }

/*
    public function gestionaParticipantes($idSorteo)
    {
        $sorteado=false;
        $numNoSorteados=self::dameNumParticipantesSinSortearConIdSorteo($idSorteo);
        if($numNoSorteados>2)//min para sortear
        {
            $arrIdParticipantesOrig=self::dameArrayIdParticipantesConIdSorteo($idSorteo);
            $arrIdSorteados=self::creaReparto($arrIdParticipantesOrig);
            self::actualizaParticipantes($idSorteo,$arrIdParticipantesOrig,$arrIdSorteados);
            $sorteado=true;
        }
        else
        {
            if($numNoSorteados==0)
            {
                $this->logger->alert("participantes ya sorteados");
                $sorteado=true;
            }
            else
            {
                if($numNoSorteados==-1)
                {
                    $this->logger->error("fallo hereedado en gestionaParticipantes");
                }
                else //1 ó 2
                {
                    $this->logger->error("no mas de 2 Participantes");
                }
            }
        }
       return $sorteado; 
    }
*/
    public function dameNumParticipantesSinSortearConIdSorteo($idSorteo)
    {   $count=-1;
        try {
            $qb = $this->em->createQueryBuilder();
            $qb->select('count(participante.id)');
            $qb->from('AppBundle:Participante','participante');
            $qb->where('participante.asignado is NULL');
            $qb->andwhere('participante.sorteo = ?1');
            $qb->setParameter(1,$idSorteo);
            $count = $qb->getQuery()->getSingleScalarResult();
            $this->logger->alert('hay '.$count.' sin sortear en el sorteo '.$idSorteo." ");
        } catch (Exception $e) {
             $this->logger->error('fallo en dameNumParticipantesSinSortearConIdSorteo '.$e.getMessage());
        }
        
        return $count;
    }
/*
    public function dameArrayIdParticipantesConIdSorteo($idSorteo)
    {
       
          try {
           $qb = $this->em->createQueryBuilder();
            $qb->select('participante.id');
            $qb->from('AppBundle:Participante','participante');
            $qb->where('participante.sorteo = ?1');
            $qb->setParameter(1,$idSorteo);
            $idParticipantes = $qb->getQuery()->getArrayResult();
        } catch (Exception $e) {
            $idParticipantes=NULL;
            $this->logger->error('error en dameArrayIdParticipantesConIdSorteo: '.$e->getMessage());
        }
        return $idParticipantes;
    }
*/
    public function creaReparto($arrIds)
    {   
        $cuentaVueltas=0;
        $arrIdsSorteadas=$arrIds;
        $tam=count($arrIds);
        do
        {
            $cuentaVueltas++;
            shuffle($arrIdsSorteadas);
        }
        while(!self::repartoValido($arrIds,$arrIdsSorteadas));
        $this->logger->warning('------------------------------------');
        self::logeaUnArrayDeIntHorizontal($arrIds,"id");
        self::logeaUnArrayDeIntHorizontal($arrIdsSorteadas,"id");
        $this->logger->warning('--------------------------------');
        $this->logger->info('en '.(string)$cuentaVueltas.' loops');  
        return $arrIdsSorteadas;
    }

    public function repartoValido($arrOrig,$arrNuevo)
    { 
        $valido=true;
        for ($i=0; $i < count($arrOrig) ; $i++) 
        { 
            if ($arrOrig[$i]==$arrNuevo[$i])//si coincide alguna pos
            {
                $valido=false;
                break;
            } 
            
        }
        return $valido;
    }
/*
    public function actualizaParticipantes($idSorteo,$arrOrig,$arrReparto)
    {
        $exito=true; 
       
       for ($i=0; $i < count($arrOrig) ; $i++) 
       { 
           if(!self::actualizaParticipante($idSorteo,$arrOrig[$i]['id'],$arrReparto[$i]['id']))
           {
                $this->logger->error('NECESARIO ROLLBACK');
                $exito=false;
                break;//al haber fallo salimos bucle

                //TODO CONTADOR PARA ELIMINAR LOS CREADOS ANTES
           }
    

       }
        if ($exito)
        {
            $this->logger->warning('ACTUALIZADOS CORRECTAMENTE LOS PARTICIPANTES');
        }       

        return $exito;
    }
*/
    /*
    public function actualizaParticipante($idSorteo,$idPart,$idReceptor)
    {   
        $exito=false;
        $formato='idSorteo: %.0f '; 
        $mensaje=sprintf($formato,$idSorteo);
      
        $formato='idPart: %.0f ';
        $mensaje.=sprintf($formato,$idPart);
        
        $formato='idReceptor: %.0f ';
        $mensaje.=sprintf($formato,$idReceptor);
        
        $this->logger->alert($mensaje); 
        try 
        {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $qb->update('AppBundle:Participante', 'p');
            $qb->set('p.sorteo','?1');
            $qb->set('p.asignado','?2');
            $qb->where('p.id = ?3');
            $qb->setParameter(1,$idSorteo);
            $qb->setParameter(2,$idReceptor);
            $qb->setParameter(3,$idPart);
            $resultado=$qb->getQuery()->execute();
            $exito=true;
            $this->logger->info('actualizado '.$resultado);
        } 
        catch (Exception $e) 
        {
             $this->logger->error('error actualizando partipantes '.$e.getMessage());
        }  
         return $exito;
    }
*/
    public function enviamosCorreosSorteo(Sorteo $sorteo)
    {
        
        $participantes=$sorteo->getParticipantes();

        foreach ($participantes as $participante) 
        {
            self::enviaCorreoParticipante(
                $participante->getNombre(),
                $participante->getCorreo(),
                $participante->getAsignado(),
                $sorteo->getAsunto(),
                $sorteo->getMensaje()
                );
        }
       
     
        
}

 public function enviaCorreoParticipante($nombre,$correo,$asignado,$asunto,$mensaje)
 {
    $enviador=Sorteo::ENVIADOR;
    //$transporter = new \Swift_SmtpTransport('smtp-relay.gmail.com');
        $transporter = new \Swift_SmtpTransport('aspmx.l.google.com');
        //$transporter = new \Swift_SmtpTransport('smtp.gmail.com');
        $mailer = new \Swift_Mailer($transporter);
       
 
          try {
                $mensaje = (new \Swift_Message($asunto));
                $mensaje->setFrom($enviador);
                $mensaje->setTo($correo);
                $mensaje->setBody('prueba'
                  /*
                      $this->renderView(
                        'default/Email.html.twig',
                        array('asunto' => $strAsunto),
                        'text/html'*/
                  );
             $result=$mailer->send($mensaje);
             
          } catch (Exception $e) {
              $this->logger->error('fallo al enviar'.$correo." ".$e->getMessage());
          }
            
            //https://swiftmailer.symfony.com/docs/sending.html
            //Using the send() Method
      return $result;
 }

     
    public function existeSesionActualGuardada()
    {
        
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
            $this->logger->error('fallo al leer el num sesion actual esta guardada: '. $e->getMessage);   
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
    public function dameArrayParticipantesNoSorteados()
    {
        
        try 
        {   
           // $em1=$this->$em->getDoctrine()->getManager();
            $participante_repo=$this->em->getRepository("AppBundle:Participante");
            $participantes= $participante_repo->findAll();
            $participantesNoSorteados= new ArrayCollection();
            foreach ($participantes as $participante) 
            {
                if (is_null($participante->getIdSorteo()))
                    $participantesNoSorteados[]=$participante;
            }
        } catch (Exception $e) {
             $idParticipantes=NULL;
           $this->logger->error('error en dameArrayParticipantesNoSorteados: '.$e->getMessage());
        }
    }

    public function guardaParticipante(Participante $participante)
    {
        $status=null;
        try 
        {
                    $this->em->persist($participante);
            $status=$this->em->flush();
        } 
        catch (Exception $e) 
        {
            $this->logger->error('error al crear nuevo participante: '.$e->getMessage());
        }
        return $status;              
    }

    private function dameCandidatoCodigo()
    {
        
        $codigo=random_int(255,999999);
        //$numDigit=strlen($codigo);
        $numDigit=6;//siempre 6 digitos
        $hoy=date("ymd");
        $numCeros=pow(10,$numDigit);
        $prefijo=$hoy*$numCeros;
        $codigo=$prefijo+$codigo;

        $this->logger->info('el candidato a codigo es '.$codigo);
        return $codigo;
    }

   
    public function generoNumeroSerieAleatorio()
    {
       // $codigos=self::dameCodigosExistentes();
        $codigos=self::dameCodigosExistentesDeEstaFecha(date("ymd"));
        if (is_null($codigos))

            $this->logger->warning('no hay codigos previos ');

        if(!is_null($codigos))//hay codigos de sorteo anteriores
        {
            do
            {
                $codigo=self::dameCandidatoCodigo();
                
            }while (!self::esCodigoValido($codigo,$codigos));    
        }
        else//no hay codigos anteriores
        {   
            $codigo=self::dameCandidatoCodigo();
        }

            $this->logger->info('codigo Serie aleatorio generado: '.$codigo);
        return $codigo;
    }

    private function dameCodigosExistentesDeEstaFecha($fecha)
    {
        $codigos;
        //$logger=$this->get('Logger');
        if(self::dameNumSorteos()>0)
        {
            try 
            {
                $qb = $this->em->createQueryBuilder('s');
                $qb->select('s.codigoSorteo');
                $qb->from('AppBundle:Sorteo','s');
                $qb->where('s.codigoSorteo IS NOT NULL');
                $qb->andwhere('s.codigoSorteo LIKE :fecha');
                $qb->setParameter('fecha',$fecha.'%');
                $codigos = $qb->getQuery()->getArrayResult();
            } 
            catch (Exception $e) 
            {

            $this->logger->error('fallo al leer sorteos '.$e->getMessage());
                $codigos=null;
            }
        }
        else
        {
            $codigos=null;

            $this->logger->info('no hay ningun codigo anterior');
        }
        self::logeaUnArrayDeInt($codigos,"codigo","codigoSorteo");

        return $codigos;
    }

    private function dameCodigosExistentes()
    { 
        $codigos;
        //$logger=$this->get('Logger');
        if(self::dameNumSorteos()>0)
        {
            try 
            {
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder('s');
                $qb->select('s.codigoSorteo');
                $qb->from('AppBundle:Sorteo','s');
                $qb->where('s.codigoSorteo IS NOT NULL');
                $codigos = $qb->getQuery()->getArrayResult();
            } 
            catch (Exception $e) 
            {

                $this->logger->error('fallo al leer sorteos '.$e->getMessage());
                $codigos=null;
            }
        }
        else
        {
            $codigos=null;
            $this->logger->info('no hay codigos anteriores');
        }
        return $codigos;
    }

    private function dameNumSorteos()
    {  
       // $logger=$this->get('Logger');
        $count;
        try {
            //$em = $this->getDoctrine()->getManager();
            $qb = $this->em->createQueryBuilder();
            $qb->select('count(Sorteo.id)');
            $qb->from('AppBundle:Sorteo','Sorteo');
            //$qb->where('Sorteo. is NOT NULL');
            $count = $qb->getQuery()->getSingleScalarResult();

            $this->logger->info('hay '.$count.' sorteos previos');
        } catch (Exception $e) {
            $count=-1;

            $this->logger->error('hay un fallo en dameNumSorteos'.$e->getMessage());
        }
         
        return $count;
    }

     private function logeaUnArrayDeInt($arrayInt,$nombreElem ="elemento",$tagArray=NULL)
    {
        if(is_null($arrayInt))
        $this->logger->info('no hay '.$nombreElem.'s ');
        else
        {
            if (count($arrayInt)==1)
            {
        
                $formato='hay un solo '.$nombreElem.': %.0f';
                if (is_null($tagArray))//array no asociativo
                {
                    $this->logger->info('hay un solo '.$nombreElem.': '.sprintf($formato,$arrayInt[0]));
                }
                else
                {
                    $this->logger->info('hay un solo '.$nombreElem.': '.sprintf($formato,$arrayInt[0][$tagArray]));//ojo array etiquetado dentro de array normal
                }
           
            }
            else
            {      
                    $this->logger->info('hay '.count($arrayInt).' '.$nombreElem.'s');
                foreach ($arrayInt as $cursor) 
                {
                    $formato=''.$nombreElem.': %.0f';
                   if (is_null($tagArray))//array no asociativo 
                   {
                        $this->logger->info(sprintf($formato,$cursor));
                   }
                   else
                   {
                        $this->logger->info(sprintf($formato,$cursor[$tagArray]));  
                   }
                }
            }
        }     
    }

    private function esCodigoValido($codigo,$arrCodigos)
    {
        try {
            $esValido=true;
            foreach ($arrCodigos as $value) {
                if ($codigo==$value)
                {
                    $esValido=false;
                }
            }
        } 
        catch (Exception $e) 
        {
            $this->logger->error('al comprobar cada codigo '.$e->getMessage());
        }
        
        return $esValido;
    }
    
     public function dameNombreActionActual(Request $request)
     {
        //$request->attributes->get('_controller') devuelve un formato tal que asi  yourBundle\Controller\yourController::CreateAction
        $arrNombreAction=explode('::',$request->attributes->get('_controller'));
        $action=$arrNombreAction[1];
        $arrController=explode("\\",$arrNombreAction[0]);
        $posController=count($arrController)-1;
        $nombre=$arrController[$posController]."/".$action;
        return $nombre;
     }

     public function dameArrayPosiciones(Sorteo $sorteo)
     {
        if(is_null($sorteo))
            $arrPosiciones=NULL;
        else
        {
            $arrPosition=array();
            $arrIndices=array();
            $arrPosiciones=array(
                "posiciones"=>$arrPosition,
                "indices"=>$arrIndices
                );

            $lenPart=count($sorteo->getParticipantes());
            for ($i=0; $i <$lenPart ; $i++) 
            { 
                $arrPosiciones['posiciones'][]=$i;
            }
            
            foreach ($sorteo->getParticipantes() as $participante) 
            {
                $arrPosiciones['indices'][]=$participante->getId();
            }  

        }
             
        return $arrPosiciones;
     }

     public function damePosiciones(Sorteo $sorteo)
     {
        $arrPosition=array();
         if(is_null($sorteo))
            $arrPosiciones=NULL;
        else
        {
            $lenPart=count($sorteo->getParticipantes());
            for ($i=0; $i <$lenPart ; $i++) 
            { 
                $arrPosition[]=$i;
            }
        }
        return $arrPosition;
       
     }

/*
    pre: $arrayInt puede ser un array int o bigInt
         $tagArray distinto de NULL si array asociativo
         solo valido para symfony 3
    post: muestra el tamaño del array en el log y cada uno de sus elementos
*/
    public function logeaUnArrayDeIntHorizontal($arrayInt,$tagArray=NULL)
    {
       
        $recogedor="";
        foreach ($arrayInt as $cursor) 
        {
                $formato=' %.0f';
           if (is_null($tagArray))//array no asociativo 
           {

                $recogedor=$recogedor.sprintf($formato,$cursor)."|"; 
           }
           else
           {
                $recogedor=$recogedor.sprintf($formato,$cursor[$tagArray])."|";
           }
        }
        $this->logger->info($recogedor);
    }

    public function logeaUnInt($int,$mensaje)
    {
        $format=' %2$s %1$d ';
        $this->logger->info(sprintf($format,$int,$mensaje));
    }

    public function logeaUnFloat($int,$mensaje)
    {
        $format=' %2$s %1$.0f ';
        $this->logger->info(sprintf($format,$int,$mensaje));
    }
}   