<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComprobanteDetalle
 *
 * @ORM\Table(name="comprobante_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ComprobanteDetalleRepository")
 */
class ComprobanteDetalle
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
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante_id", referencedColumnName="id")
     * })
     */
    private $comprobante;

    /**
     * @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="articulo_id", referencedColumnName="id")
     * })
     */
    private $articulo;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="bonificacion", type="decimal", precision=16, scale=3)
     */
    private $bonificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_unitario", type="decimal", precision=16, scale=3)
     */
    private $precioUnitario;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=3)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="total_no_gravado", type="decimal", precision=16, scale=3)
     */
    private $totalNoGravado;

    /**
     * @var string
     *
     * @ORM\Column(name="total_neto", type="decimal", precision=16, scale=3)
     */
    private $totalNeto;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva_exento", type="decimal", precision=16, scale=3)
     */
    private $importeIvaExento;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva", type="decimal", precision=16, scale=3)
     */
    private $importeIva;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_tributos", type="decimal", precision=16, scale=3)
     */
    private $importeTributos;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_costo", type="decimal", precision=16, scale=3)
     */
    private $precioCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="ganancia", type="decimal", precision=16, scale=3)
     */
    private $ganancia;

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
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return ComprobanteDetalle
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set bonificacion
     *
     * @param string $bonificacion
     *
     * @return ComprobanteDetalle
     */
    public function setBonificacion($bonificacion)
    {
        $this->bonificacion = $bonificacion;

        return $this;
    }

    /**
     * Get bonificacion
     *
     * @return string
     */
    public function getBonificacion()
    {
        return $this->bonificacion;
    }

    /**
     * Set precioUnitario
     *
     * @param string $precioUnitario
     *
     * @return ComprobanteDetalle
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return string
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return ComprobanteDetalle
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set totalNoGravado
     *
     * @param string $totalNoGravado
     *
     * @return ComprobanteDetalle
     */
    public function setTotalNoGravado($totalNoGravado)
    {
        $this->totalNoGravado = $totalNoGravado;

        return $this;
    }

    /**
     * Get totalNoGravado
     *
     * @return string
     */
    public function getTotalNoGravado()
    {
        return $this->totalNoGravado;
    }

    /**
     * Set totalNeto
     *
     * @param string $totalNeto
     *
     * @return ComprobanteDetalle
     */
    public function setTotalNeto($totalNeto)
    {
        $this->totalNeto = $totalNeto;

        return $this;
    }

    /**
     * Get totalNeto
     *
     * @return string
     */
    public function getTotalNeto()
    {
        return $this->totalNeto;
    }

    /**
     * Set importeIvaExento
     *
     * @param string $importeIvaExento
     *
     * @return ComprobanteDetalle
     */
    public function setImporteIvaExento($importeIvaExento)
    {
        $this->importeIvaExento = $importeIvaExento;

        return $this;
    }

    /**
     * Get importeIvaExento
     *
     * @return string
     */
    public function getImporteIvaExento()
    {
        return $this->importeIvaExento;
    }

    /**
     * Set importeIva
     *
     * @param string $importeIva
     *
     * @return ComprobanteDetalle
     */
    public function setImporteIva($importeIva)
    {
        $this->importeIva = $importeIva;

        return $this;
    }

    /**
     * Get importeIva
     *
     * @return string
     */
    public function getImporteIva()
    {
        return $this->importeIva;
    }

    /**
     * Set importeTributos
     *
     * @param string $importeTributos
     *
     * @return ComprobanteDetalle
     */
    public function setImporteTributos($importeTributos)
    {
        $this->importeTributos = $importeTributos;

        return $this;
    }

    /**
     * Get importeTributos
     *
     * @return string
     */
    public function getImporteTributos()
    {
        return $this->importeTributos;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return ComprobanteDetalle
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
     * Set precioCosto
     *
     * @param string $precioCosto
     *
     * @return ComprobanteDetalle
     */
    public function setPrecioCosto($precioCosto)
    {
        $this->precioCosto = $precioCosto;

        return $this;
    }

    /**
     * Get precioCosto
     *
     * @return string
     */
    public function getPrecioCosto()
    {
        return $this->precioCosto;
    }

    /**
     * Set ganancia
     *
     * @param string $ganancia
     *
     * @return ComprobanteDetalle
     */
    public function setGanancia($ganancia)
    {
        $this->ganancia = $ganancia;

        return $this;
    }

    /**
     * Get ganancia
     *
     * @return string
     */
    public function getGanancia()
    {
        return $this->ganancia;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return ComprobanteDetalle
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
     * @return ComprobanteDetalle
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
     * @return ComprobanteDetalle
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
     * @return ComprobanteDetalle
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
     * @return ComprobanteDetalle
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
     * Set comprobante
     *
     * @param \AppBundle\Entity\Comprobante $comprobante
     *
     * @return ComprobanteDetalle
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

    /**
     * Set articulo
     *
     * @param \AppBundle\Entity\Articulo $articulo
     *
     * @return ComprobanteDetalle
     */
    public function setArticulo(\AppBundle\Entity\Articulo $articulo = null)
    {
        $this->articulo = $articulo;

        return $this;
    }

    /**
     * Get articulo
     *
     * @return \AppBundle\Entity\Articulo
     */
    public function getArticulo()
    {
        return $this->articulo;
    }
}
