<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenPagoComprobante
 *
 * @ORM\Table(name="orden_pago_comprobante")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenPagoComprobanteRepository")
 */
class OrdenPagoComprobante
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
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante_id", referencedColumnName="id")
     * })
     */
    private $comprobante;
    
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * @return OrdenPagoComprobante
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
     * Set comprobante
     *
     * @param \AppBundle\Entity\Comprobante $comprobante
     *
     * @return OrdenPagoComprobante
     */
    public function setComprobante(\AppBundle\Entity\Comprobante $comprobante = null)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \AppBundle\Entity\Comprobante
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}
