<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClienteRepository")
 */
class Cliente
{
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var \AfipDocumentoTipo
     *
     * @ORM\ManyToOne(targetEntity="AfipDocumentoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="documento_tipo_id", referencedColumnName="id")
     * })
     */
    private $documentoTipo;

    /**
     * @var string
     *
     * @ORM\Column(name="documento_numero", type="string", unique=true, length=255)
     */
    private $documentoNumero;

    /**
     * @var \AfipIvaCondicion
     *
     * @ORM\ManyToOne(targetEntity="AfipIvaCondicion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iva_condicion_id", referencedColumnName="id")
     * })
     */
    private $ivaCondicion;

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
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="contacto", type="string", length=255, nullable=true)
     */
    private $contacto;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    //saldo = importe actual de la cuenta del cliente
    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="decimal", precision=16, scale=2)
     */
    private $saldo;

    /**
     * @var \ObraSocialPlan
     *
     * @ORM\ManyToOne(targetEntity="ObraSocialPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="obra_social_plan_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $obraSocialPlan;

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
     * @return Cliente
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
     * Set documentoNumero
     *
     * @param string $documentoNumero
     *
     * @return Cliente
     */
    public function setDocumentoNumero($documentoNumero)
    {
        $this->documentoNumero = $documentoNumero;

        return $this;
    }

    /**
     * Get documentoNumero
     *
     * @return string
     */
    public function getDocumentoNumero()
    {
        return $this->documentoNumero;
    }

    /**
     * Set ivaCondicion
     *
     * @param string $ivaCondicion
     *
     * @return Cliente
     */
    public function setIvaCondicion($ivaCondicion)
    {
        $this->ivaCondicion = $ivaCondicion;

        return $this;
    }

    /**
     * Get ivaCondicion
     *
     * @return string
     */
    public function getIvaCondicion()
    {
        return $this->ivaCondicion;
    }

    /**
     * Set localidadId
     *
     * @param integer $localidadId
     *
     * @return Cliente
     */
    public function setLocalidadId($localidadId)
    {
        $this->localidadId = $localidadId;

        return $this;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Cliente
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
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Cliente
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
     * Set email
     *
     * @param string $email
     *
     * @return Cliente
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set contacto
     *
     * @param string $contacto
     *
     * @return Cliente
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;

        return $this;
    }

    /**
     * Get contacto
     *
     * @return string
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Cliente
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
     * @return Cliente
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
     * @return Cliente
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
     * Set localidad
     *
     * @param \AppBundle\Entity\Localidad $localidad
     *
     * @return Cliente
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

    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Set documentoTipo
     *
     * @param \AppBundle\Entity\AfipDocumentoTipo $documentoTipo
     *
     * @return Cliente
     */
    public function setDocumentoTipo(\AppBundle\Entity\AfipDocumentoTipo $documentoTipo = null)
    {
        $this->documentoTipo = $documentoTipo;

        return $this;
    }

    /**
     * Get documentoTipo
     *
     * @return \AppBundle\Entity\AfipDocumentoTipo
     */
    public function getDocumentoTipo()
    {
        return $this->documentoTipo;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return Cliente
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
     * @return Cliente
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
     * Set saldo
     *
     * @param string $saldo
     *
     * @return Cliente
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set obraSocialPlan
     *
     * @param \AppBundle\Entity\ObraSocialPlan $obraSocialPlan
     *
     * @return Cliente
     */
    public function setObraSocialPlan(\AppBundle\Entity\ObraSocialPlan $obraSocialPlan = null)
    {
        $this->obraSocialPlan = $obraSocialPlan;

        return $this;
    }

    /**
     * Get obraSocialPlan
     *
     * @return \AppBundle\Entity\ObraSocialPlan
     */
    public function getObraSocialPlan()
    {
        return $this->obraSocialPlan;
    }
}
