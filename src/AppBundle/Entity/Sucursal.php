<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sucursal
 *
 * @ORM\Table(name="sucursal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SucursalRepository")
 */
class Sucursal
{
    const CASA_CENTRAL = 1;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @var \Localidad
     *
     * @ORM\ManyToOne(targetEntity="Localidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="localidad_id", referencedColumnName="id")
     * })
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="whatsapp", type="string", length=255, nullable=true)
     */
    private $whatsapp;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_optico", type="string", length=255, nullable=true)
     */
    private $tecnicoOptico;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_optico_matricula", type="string", length=255, nullable=true)
     */
    private $tecnicoOpticoMatricula;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_contactologo", type="string", length=255, nullable=true)
     */
    private $tecnicoContactologo;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_contactologo_matricula", type="string", length=255, nullable=true)
     */
    private $tecnicoContactologoMatricula;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    public function __toString()
    {
        return $this->nombre;
    }
    
    /**
     * Get id
     *
     * @return int
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
     * @return Sucursal
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
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Localidad
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Localidad
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Localidad
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return Sucursal
     */
    public function setCreatedBy(\AppBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \AppBundle\Entity\Usuario $updatedBy
     *
     * @return Sucursal
     */
    public function setUpdatedBy(\AppBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Sucursal
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set localidad
     *
     * @param \AppBundle\Entity\Localidad $localidad
     *
     * @return Sucursal
     */
    public function setLocalidad(\AppBundle\Entity\Localidad $localidad = null)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return \AppBundle\Entity\Localidad
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Sucursal
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set whatsapp
     *
     * @param string $whatsapp
     *
     * @return Sucursal
     */
    public function setWhatsapp($whatsapp)
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }

    /**
     * Get whatsapp
     *
     * @return string
     */
    public function getWhatsapp()
    {
        return $this->whatsapp;
    }

    /**
     * Set tecnicoOptico
     *
     * @param string $tecnicoOptico
     *
     * @return Sucursal
     */
    public function setTecnicoOptico($tecnicoOptico)
    {
        $this->tecnicoOptico = $tecnicoOptico;

        return $this;
    }

    /**
     * Get tecnicoOptico
     *
     * @return string
     */
    public function getTecnicoOptico()
    {
        return $this->tecnicoOptico;
    }

    /**
     * Set tecnicoOpticoMatricula
     *
     * @param string $tecnicoOpticoMatricula
     *
     * @return Sucursal
     */

    public function setTecnicoOpticoMatricula($tecnicoOpticoMatricula)
    {
        $this->tecnicoOpticoMatricula = $tecnicoOpticoMatricula;

        return $this;
    }

    /**
     * Get tecnicoOpticoMatricula
     *
     * @return string
     */
    public function getTecnicoOpticoMatricula()
    {
        return $this->tecnicoOpticoMatricula;
    }

    /**
     * Set tecnicoContactologo
     *
     * @param string $tecnicoContactologo
     *
     * @return Sucursal
     */
    public function setTecnicoContactologo($tecnicoContactologo)
    {
        $this->tecnicoContactologo = $tecnicoContactologo;

        return $this;
    }

    /**
     * Get tecnicoContactologo
     *
     * @return string
     */
    public function getTecnicoContactologo()
    {
        return $this->tecnicoContactologo;
    }

    /**
     * Set tecnicoContactologo
     *
     * @param string $tecnicoContactologo
     *
     * @return Sucursal
     */
    public function setTecnicoContactologoMatricula($tecnicoContactologoMatricula)
    {
        $this->tecnicoContactologoMatricula = $tecnicoContactologoMatricula;

        return $this;
    }

    /**
     * Get tecnicoContactologoMatricula
     *
     * @return string
     */
    public function getTecnicoContactologoMatricula()
    {
        return $this->tecnicoContactologoMatricula;
    }

}
