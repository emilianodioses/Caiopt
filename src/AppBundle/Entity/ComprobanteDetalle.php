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

    //Movimiento = Tipo de movimiento, "Compra" o "Venta"
    /**
     * @var string
     *
     * @ORM\Column(name="movimiento", type="string", length=255)
     */
    private $movimiento;

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
     * @ORM\Column(name="precio_unitario", type="decimal", precision=16, scale=2)
     */
    //VENTA: precio del art. sin iva sin bonificacion
    //COMPRA: precio del art. sin iva sin bonificacion
    private $precioUnitario;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje_bonificacion", type="decimal", precision=16, scale=2)
     */
    //VENTA: porcentaje de bonificacion del art.
    //COMPRA: porcentaje de bonificacion del art.
    private $porcentajeBonificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_bonificacion", type="decimal", precision=16, scale=2)
     */
    //VENTA: porcentaje de bonificacion sobre el precio unitario
    //COMPRA: porcentaje de bonificacion sobre el precio unitario
    private $importeBonificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje_iva", type="decimal", precision=16, scale=2)
     */
    //VENTA: porcentaje de iva del art.
    //COMPRA: porcentaje de iva del art.
    private $porcentajeIva;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva", type="decimal", precision=16, scale=2)
     */
    //VENTA: importe del iva del art. sobre precio_unitario ¿+ importe_bonificacion?
    //COMPRA: importe del iva del art. sobre precio_unitario ¿+ importe_bonificacion?
    private $importeIva; //importeIva = totalNeto*IVA

    /**
     * @var string
     *
     * @ORM\Column(name="precio_costo", type="decimal", precision=16, scale=2)
     */
    //VENTA: precio_de_compra(obtenido del art.) + importe_iva + bonificacion (¿¿¿va importe_iva???)
    //COMPRA: precio_unitario + bonificacion
    private $precioCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_venta", type="decimal", precision=16, scale=2)
     */
    //VENTA: precio_unitario + importe_iva + importe_bonificacion
    //COMPRA: precio_costo + iva + importe_ganancia
    private $precioVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_neto", type="decimal", precision=16, scale=2)
     */
    //VENTA: precio_unitario + importe_bonificacion
    private $precioNeto;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_ganancia", type="decimal", precision=16, scale=2)
     */
    //VENTA: precio_venta - precio_costo
    //COMPRA: 0 (no se utiliza)
    private $importeGanancia;

    /**
     * @var string
     *
     * @ORM\Column(name="porcentaje_ganancia", type="decimal", precision=16, scale=2)
     */
    //VENTA: 0 (no se utiliza)
    //COMPRA: precio_costo + importe_ganancia
    private $porcentajeGanancia;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_neto", type="decimal", precision=16, scale=2)
     */
    //VENTA: cantidad * precio_unitario (sin iva)
    //COMPRA: cantidad * precio_costo (sin iva)
    private $totalNeto;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    //VENTA: cantidad * (precio_unitario + importe_bonificacion + importe_iva)
    //COMPRA: cantidad * (precio_costo + importe_iva)
    private $total; 

    /**
     * @var string
     *
     * @ORM\Column(name="total_no_gravado", type="decimal", precision=16, scale=2)
     */
    //totalNoGravado = 0 (no se utiliza)
    private $totalNoGravado = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva_exento", type="decimal", precision=16, scale=2)
     */
    //importeIvaExento = 0  (no se utiliza)
    private $importeIvaExento = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set movimiento
     *
     * @param string $movimiento
     *
     * @return ComprobanteDetalle
     */
    public function setMovimiento($movimiento)
    {
        $this->movimiento = $movimiento;

        return $this;
    }

    /**
     * Get movimiento
     *
     * @return string
     */
    public function getMovimiento()
    {
        return $this->movimiento;
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
     * Set porcentajeBonificacion
     *
     * @param string $porcentajeBonificacion
     *
     * @return ComprobanteDetalle
     */
    public function setPorcentajeBonificacion($porcentajeBonificacion)
    {
        $this->porcentajeBonificacion = $porcentajeBonificacion;

        return $this;
    }

    /**
     * Get porcentajeBonificacion
     *
     * @return string
     */
    public function getPorcentajeBonificacion()
    {
        return $this->porcentajeBonificacion;
    }

    /**
     * Set importeBonificacion
     *
     * @param string $importeBonificacion
     *
     * @return ComprobanteDetalle
     */
    public function setImporteBonificacion($importeBonificacion)
    {
        $this->importeBonificacion = $importeBonificacion;

        return $this;
    }

    /**
     * Get importeBonificacion
     *
     * @return string
     */
    public function getImporteBonificacion()
    {
        return $this->importeBonificacion;
    }

    /**
     * Set porcentajeIva
     *
     * @param string $porcentajeIva
     *
     * @return ComprobanteDetalle
     */
    public function setPorcentajeIva($porcentajeIva)
    {
        $this->porcentajeIva = $porcentajeIva;

        return $this;
    }

    /**
     * Get porcentajeIva
     *
     * @return string
     */
    public function getPorcentajeIva()
    {
        return $this->porcentajeIva;
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
     * Set precioVenta
     *
     * @param string $precioVenta
     *
     * @return ComprobanteDetalle
     */
    public function setPrecioVenta($precioVenta)
    {
        $this->precioVenta = $precioVenta;

        return $this;
    }

    /**
     * Get precioVenta
     *
     * @return string
     */
    public function getPrecioVenta()
    {
        return $this->precioVenta;
    }

    /**
     * Set importeGanancia
     *
     * @param string $importeGanancia
     *
     * @return ComprobanteDetalle
     */
    public function setImporteGanancia($importeGanancia)
    {
        $this->importeGanancia = $importeGanancia;

        return $this;
    }

    /**
     * Get importeGanancia
     *
     * @return string
     */
    public function getImporteGanancia()
    {
        return $this->importeGanancia;
    }

    /**
     * Set porcentajeGanancia
     *
     * @param string $porcentajeGanancia
     *
     * @return ComprobanteDetalle
     */
    public function setPorcentajeGanancia($porcentajeGanancia)
    {
        $this->porcentajeGanancia = $porcentajeGanancia;

        return $this;
    }

    /**
     * Get porcentajeGanancia
     *
     * @return string
     */
    public function getPorcentajeGanancia()
    {
        return $this->porcentajeGanancia;
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

    /**
     * Set precioNeto
     *
     * @param string $precioNeto
     *
     * @return ComprobanteDetalle
     */
    public function setPrecioNeto($precioNeto)
    {
        $this->precioNeto = $precioNeto;

        return $this;
    }

    /**
     * Get precioNeto
     *
     * @return string
     */
    public function getPrecioNeto()
    {
        return $this->precioNeto;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return ComprobanteDetalle
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
     * @return ComprobanteDetalle
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
