<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoInterno
 *
 * @ORM\Table(name="movimiento_interno")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovimientoInternoRepository")
 */
class MovimientoInterno
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
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sucursal_origen", referencedColumnName="id", nullable=false)
     * })
     */
    private $sucursalOrigen;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sucursal_destino", referencedColumnName="id", nullable=false)
     * })
     */
    private $sucursalDestino;

    /**
     * @var string
     *
     * @ORM\Column(name="monto", type="decimal", precision=16, scale=2)
     */
    private $monto;

    /**
     * @var int
     *
     * @ORM\Column(name="movimiento_categoria", type="integer")
     */

    /**
     * @var \MovimientoCategoria
     *
     * @ORM\ManyToOne(targetEntity="MovimientoCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movimiento_categoria", referencedColumnName="id", nullable=false)
     * })
     */
    private $movimientoCategoria;

    /**
     * @var \PagoTipo
     *
     * @ORM\ManyToOne(targetEntity="PagoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pago_tipo", referencedColumnName="id", nullable=true)
     * })
     */
    private $pagoTipo;

    /**
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante", referencedColumnName="id", nullable=true)
     * })
     */
    private $comprobante;
  
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

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
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    public function __toString()
    {
        return $this->id.' ';
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Comprobante
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sucursalOrigen
     *
     * @param integer $sucursalOrigen
     *
     * @return MovimientoInterno
     */
    public function setSucursalOrigen($sucursalOrigen)
    {
        $this->sucursalOrigen = $sucursalOrigen;

        return $this;
    }

    /**
     * Get sucursalOrigen
     *
     * @return int
     */
    public function getSucursalOrigen()
    {
        return $this->sucursalOrigen;
    }

    /**
     * Set sucursalDestino
     *
     * @param integer $sucursalDestino
     *
     * @return MovimientoInterno
     */
    public function setSucursalDestino($sucursalDestino)
    {
        $this->sucursalDestino = $sucursalDestino;

        return $this;
    }

    /**
     * Get sucursalDestino
     *
     * @return int
     */
    public function getSucursalDestino()
    {
        return $this->sucursalDestino;
    }

    /**
     * Set monto
     *
     * @param string $monto
     *
     * @return MovimientoInterno
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set movimientoCategoria
     *
     * @param integer $movimientoCategoria
     *
     * @return MovimientoInterno
     */
    public function setMovimientoCategoria($movimientoCategoria)
    {
        $this->movimientoCategoria = $movimientoCategoria;

        return $this;
    }

    /**
     * Get movimientoCategoria
     *
     * @return MovimientoInterno
     */
    public function getMovimientoCategoria()
    {
        return $this->movimientoCategoria;
    }

    /**
     * Set pagoTipo
     *
     * @param integer $pagoTipo
     *
     * @return PagoTipo
     */
    public function setPagoTipo($pagoTipo)
    {
        $this->pagoTipo = $pagoTipo;

        return $this;
    }

    /**
     * Get movimientoCategoria
     *
     * @return PagoTipo
     */
    public function getPagoTipo()
    {
        return $this->pagoTipo;
    }

    /**
     * Set comprobante
     *
     * @param integer $comprobante
     *
     * @return MovimientoInterno
     */
    public function setComprobante($comprobante)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return int
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return MovimientoInterno
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return MovimientoInterno
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
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
     * @return MovimientoInterno
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
     * @return MovimientoInterno
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return int
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
     * @return MovimientoInterno
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return MovimientoInterno
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }
}

