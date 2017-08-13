<?php
//AppBundle\Entity\Sorteo.php
namespace AppBundle\Entity;

use AppBundle\Entity\Sorteo;
use AppBundle\Entity\Participante; 
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Sorteo
 */
class Sorteo
{

    const ENVIADOR="hegemon70@gmail.com";
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $codigoSorteo;

    /**
     * @var string
     */
    private $mensaje;

    /**
     * @var string
     */
    private $asunto;


    protected $participante;

    public function __construct()
    {
        $this->participante = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigoSorteo
     *
     * @param integer $codigoSorteo
     *
     * @return Sorteo
     */
    public function setCodigoSorteo($codigoSorteo)
    {
        $this->codigoSorteo = $codigoSorteo;

        return $this;
    }

    /**
     * Get codigoSorteo
     *
     * @return integer
     */
    public function getCodigoSorteo()
    {
        return $this->codigoSorteo;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     *
     * @return Sorteo
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set asunto
     *
     * @param string $asunto
     *
     * @return Sorteo
     */
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get asunto
     *
     * @return string
     */
    public function getAsunto()
    {
        return $this->asunto;
    }

    /**
     * Set participante
     *
     * @param Participante $participante
     *
     * @return Sorteo
     */
     public function setParticipante(Participante $participante)
    {
        $this->participante[]=$participante;
        return $this;
    }

    /**
     * Get participantes
     *
     * @return Participente
     */
    public function getParticipantes()
    {
        return $this->participante;
    }


         //Metodo MAGICO creado para devolver el nombre 
    public function __toString() {
        $format='%.0u';
        return sprintf($format,$this->codigoSorteo);
    }

    /**
     * Add participante
     *
     * @param \AppBundle\Entity\Participante $participante
     *
     * @return Sorteo
     */
    public function addParticipante(\AppBundle\Entity\Participante $participante)
    {
        $this->participante[] = $participante;

        return $this;
    }

    /**
     * Remove participante
     *
     * @param \AppBundle\Entity\Participante $participante
     */
    public function removeParticipante(\AppBundle\Entity\Participante $participante)
    {
        $this->participante->removeElement($participante);
    }

    /**
     * Get participante
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipante()
    {
        return $this->participante;
    }
}
