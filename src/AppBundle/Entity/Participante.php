<?php
//AppBundle\Entity\Participante.php
namespace AppBundle\Entity;

use AppBundle\Entity\Sorteo;
use AppBundle\Entity\Participante;

/**
 * Participante
 */
class Participante
{

    const NUM_PART=1;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $correo;

    /**
     * @var integer
     */
    private $asignado;

    /**
     * @var \AppBundle\Entity\Sorteo
     */
    private $idSorteo;

    /**
     * @var integer
     */
    private $position;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Participante
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Participante
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set asignado
     *
     * @param integer $asignado
     *
     * @return Participante
     */
    public function setAsignado($asignado)
    {
        $this->asignado = $asignado;

        return $this;
    }

    /**
     * Get asignado
     *
     * @return integer
     */
    public function getAsignado()
    {
        return $this->asignado;
    }

    /**
     * Set idSorteo
     *
     * @param \AppBundle\Entity\Sorteo $idSorteo
     *
     * @return Participante
     */
    public function setIdSorteo( Sorteo $idSorteo = null)
    {
        $this->idSorteo = $idSorteo;

        return $this;
    }

    /**
     * Get idSorteo
     *
     * @return \AppBundle\Entity\Sorteo
     */
    public function getIdSorteo()
    {
        return $this->idSorteo;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Participante
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
     
   
      //Metodo MAGICO creado para devolver el nombre 
    public function __toString() 
    {
        return $this->nombre;
    }

    

}
