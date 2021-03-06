<?php

namespace AppBundle\Entity;

/**
 * Sesion
 */
class Sesion
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $codSesion;

    /**
     * @var \DateTime
     */
    private $fechaCreacion = 'CURRENT_TIMESTAMP';

    /**
     * @var \AppBundle\Entity\Sorteo
     */
    private $idSorteo;


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
     * Set codSesion
     *
     * @param string $codSesion
     *
     * @return Sesion
     */
    public function setCodSesion($codSesion)
    {
        $this->codSesion = $codSesion;

        return $this;
    }

    /**
     * Get codSesion
     *
     * @return string
     */
    public function getCodSesion()
    {
        return $this->codSesion;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Sesion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set idSorteo
     *
     * @param \AppBundle\Entity\Sorteo $idSorteo
     *
     * @return Sesion
     */
    public function setIdSorteo(\AppBundle\Entity\Sorteo $idSorteo = null)
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
}
