<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProveedorPago
 *
 * @ORM\Table(name="proveedor_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProveedorPagoRepository")
 */
class ProveedorPago
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
     * @var \OrdenPago
     *
     * @ORM\ManyToOne(targetEntity="OrdenPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orden_pago_id", referencedColumnName="id")
     * })
     */
    private $ordenPago;
    
    /**
     * @var \PagoTipo
     *
     * @ORM\ManyToOne(targetEntity="PagoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pago_tipo_id", referencedColumnName="id")
     * })
     */
    private $pagoTipo;

    /**
     * @var string
     *
     * @ORM\Column(name="importe", type="decimal", precision=16, scale=2)
     */
    private $importe;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="updated_by", type="integer")
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return ProveedorPago
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return ProveedorPago
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return ProveedorPago
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProveedorPago
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
     * Set updatedBy
     *
     * @param integer $updatedBy
     *
     * @return ProveedorPago
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ProveedorPago
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
     * Set ordenPago
     *
     * @param \AppBundle\Entity\OrdenPago $ordenPago
     *
     * @return ProveedorPago
     */
    public function setOrdenPago(\AppBundle\Entity\OrdenPago $ordenPago = null)
    {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \AppBundle\Entity\OrdenPago
     */
    public function getOrdenPago()
    {
        return $this->ordenPago;
    }

    /**
     * Set pagoTipo
     *
     * @param \AppBundle\Entity\PagoTipo $pagoTipo
     *
     * @return ProveedorPago
     */
    public function setPagoTipo(\AppBundle\Entity\PagoTipo $pagoTipo = null)
    {
        $this->pagoTipo = $pagoTipo;

        return $this;
    }

    /**
     * Get pagoTipo
     *
     * @return \AppBundle\Entity\PagoTipo
     */
    public function getPagoTipo()
    {
        return $this->pagoTipo;
    }
}
