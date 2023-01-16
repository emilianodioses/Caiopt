<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PuntoVenta
 *
 * @ORM\Table(name="punto_venta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PuntoVentaRepository")
 */
class PuntoVenta
{
    const PUNTO_15 = 2;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var bool
     *
     * @ORM\Column(name="fe_habilitada", type="boolean")
     */
    private $feHabilitada;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id")
     * })
     */
    private $sucursal;

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
     * Set numero
     *
     * @param integer $numero
     *
     * @return PuntoVenta
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set feHabilitada
     *
     * @param boolean $feHabilitada
     *
     * @return PuntoVenta
     */
    public function setFeHabilitada($feHabilitada)
    {
        $this->feHabilitada = $feHabilitada;

        return $this;
    }

    /**
     * Get feHabilitada
     *
     * @return bool
     */
    public function getFeHabilitada()
    {
        return $this->feHabilitada;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return PuntoVenta
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
     * @return PuntoVenta
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
     * @return PuntoVenta
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
     * Set sucursal
     *
     * @param \AppBundle\Entity\Sucursal $sucursal
     *
     * @return PuntoVenta
     */
    public function setSucursal(\AppBundle\Entity\Sucursal $sucursal = null)
    {
        $this->sucursal = $sucursal;

        return $this;
    }

    /**
     * Get sucursal
     *
     * @return \AppBundle\Entity\Sucursal
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return PuntoVenta
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
     * @return PuntoVenta
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
}
