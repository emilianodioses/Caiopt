<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibroCajaDetalle
 *
 * @ORM\Table(name="libro_caja_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LibroCajaDetalleRepository")
 */
class LibroCajaDetalle
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
     * @var \LibroCaja
     *
     * @ORM\ManyToOne(targetEntity="LibroCaja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="libro_caja_id", referencedColumnName="id")
     * })
     */
    private $libroCaja;

    /**
     * @var string
     *
     * @ORM\Column(name="origen", type="string", length=255)
     */
    private $origen;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

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
     * @var \ClientePago
     *
     * @ORM\ManyToOne(targetEntity="ClientePago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_pago_id", referencedColumnName="id")
     * })
     */
    private $clientePago;

    /**
     * @var \ProveedorPago
     *
     * @ORM\ManyToOne(targetEntity="ProveedorPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proveedor_pago_id", referencedColumnName="id")
     * })
     */
    private $proveedorPago;

    /**
     * @var \MovimientoInterno
     *
     * @ORM\ManyToOne(targetEntity="MovimientoInterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movimiento_interno", referencedColumnName="id")
     * })
     */
    private $movimientoInterno;

    /**
     * @var \MovimientoCategoria
     *
     * @ORM\ManyToOne(targetEntity="MovimientoCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movimiento_categoria_id", referencedColumnName="id")
     * })
     */
    private $movimientoCategoria;

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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return LibroCajaDetalle
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return LibroCajaDetalle
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
     * @return LibroCajaDetalle
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
     * @return LibroCajaDetalle
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
     * @return LibroCajaDetalle
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
     * @return LibroCajaDetalle
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
     * @return LibroCajaDetalle
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
     * Set libroCaja
     *
     * @param \AppBundle\Entity\LibroCaja $libroCaja
     *
     * @return LibroCajaDetalle
     */
    public function setLibroCaja(\AppBundle\Entity\LibroCaja $libroCaja = null)
    {
        $this->libroCaja = $libroCaja;

        return $this;
    }

    /**
     * Get libroCaja
     *
     * @return \AppBundle\Entity\LibroCaja
     */
    public function getLibroCaja()
    {
        return $this->libroCaja;
    }

    /**
     * Set pagoTipo
     *
     * @param \AppBundle\Entity\PagoTipo $pagoTipo
     *
     * @return LibroCajaDetalle
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

    /**
     * Set clientePago
     *
     * @param \AppBundle\Entity\ClientePago $clientePago
     *
     * @return LibroCajaDetalle
     */
    public function setClientePago(\AppBundle\Entity\ClientePago $clientePago = null)
    {
        $this->clientePago = $clientePago;

        return $this;
    }

    /**
     * Get clientePago
     *
     * @return \AppBundle\Entity\ClientePago
     */
    public function getClientePago()
    {
        return $this->clientePago;
    }

    /**
     * Set proveedorPago
     *
     * @param \AppBundle\Entity\ProveedorPago $proveedorPago
     *
     * @return LibroCajaDetalle
     */
    public function setProveedorPago(\AppBundle\Entity\ProveedorPago $proveedorPago = null)
    {
        $this->proveedorPago = $proveedorPago;

        return $this;
    }

    /**
     * Get proveedorPago
     *
     * @return \AppBundle\Entity\ProveedorPago
     */
    public function getProveedorPago()
    {
        return $this->proveedorPago;
    }

    /**
     * Set origen
     *
     * @param string $origen
     *
     * @return LibroCajaDetalle
     */
    public function setOrigen($origen)
    {
        $this->origen = $origen;

        return $this;
    }

    /**
     * Get origen
     *
     * @return string
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return LibroCajaDetalle
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set movimientoCategoria
     *
     * @param \AppBundle\Entity\MovimientoCategoria $movimientoCategoria
     *
     * @return LibroCajaDetalle
     */
    public function setMovimientoCategoria(\AppBundle\Entity\MovimientoCategoria $movimientoCategoria = null)
    {
        $this->movimientoCategoria = $movimientoCategoria;

        return $this;
    }

    /**
     * Get movimientoCategoria
     *
     * @return \AppBundle\Entity\MovimientoCategoria
     */
    public function getMovimientoCategoria()
    {
        return $this->movimientoCategoria;
    }

    /**
     * Set movimientoInterno
     *
     * @param \AppBundle\Entity\MovimientoInterno $movimientoInterno
     *
     * @return LibroCajaDetalle
     */
    public function setMovimientoInterno(\AppBundle\Entity\MovimientoInterno $movimientoInterno = null)
    {
        $this->movimientoInterno = $movimientoInterno;

        return $this;
    }

    /**
     * Get movimientoInterno
     *
     * @return \AppBundle\Entity\MovimientoInterno
     */
    public function getMovimientoInterno()
    {
        return $this->movimientoInterno;
    }
}
