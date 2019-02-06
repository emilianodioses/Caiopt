<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comprobante
 *
 * @ORM\Table(name="comprobante")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ComprobanteRepository")
 */
class Comprobante
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
     * @var \Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proveedor_id", referencedColumnName="id")
     * })
     */
    private $proveedor;

    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;

    //Tipo: Tipo de factura "A, B, ..."
    /**
     * @var \AfipComprobanteTipo
     *
     * @ORM\ManyToOne(targetEntity="AfipComprobanteTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_id", referencedColumnName="id")
     * })
     */
    private $tipo;


    //Movimiento = Tipo de movimiento, "Compra" o "Venta"
    /**
     * @var string
     *
     * @ORM\Column(name="movimiento", type="string", length=255)
     */
    private $movimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="punto_venta", type="integer")
     */
    private $puntoVenta;

    //Condicion de venta: Efectivo, tarjeta de debito/credito
    /**
     * @var \AfipCondicionVenta
     *
     * @ORM\ManyToOne(targetEntity="AfipCondicionVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condicion_venta_id", referencedColumnName="id")
     * })
     */
    private $condicionVenta;

    //Numero interno de movimiento
    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    //Numero de factura otorgado por el WS de FE de afip
    /**
     * @var int
     *
     * @ORM\Column(name="afip_numero", type="integer", nullable=true))
     */
    private $afipNumero;

    //TotalBonificacion  = Monto de dinero bonificado
    /**
     * @var string
     *
     * @ORM\Column(name="total_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $totalBonificacion = '0';

    //Total = Importe con Iva + Tributos + bonificacion + precio venta/compra
    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    private $total = '0';

    //totalNoGravado = total de los articulos que no usan IVA.
    /**
     * @var string
     *
     * @ORM\Column(name="total_no_gravado", type="decimal", precision=16, scale=2)
     */
    private $totalNoGravado = '0';

    // totalNeto: precio de venta o compra sin Iva
    /**
     * @var string
     *
     * @ORM\Column(name="total_neto", type="decimal", precision=16, scale=2)
     */
    private $totalNeto = '0';

    //importeIvaExento = es el monto de iva que por ley quedan exentos
    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva_exento", type="decimal", precision=16, scale=2)
     */
    private $importeIvaExento = '0';

    //importeIva = suma de IVA en dinero
    /**
     * @var string
     *
     * @ORM\Column(name="importe_iva", type="decimal", precision=16, scale=2)
     */
    private $importeIva = '0';

    //importeTributos = es la sumatoria de otros tributos excepto IVA por ejemplo ingresos brutos (IIBB)
    /**
     * @var string
     *
     * @ORM\Column(name="importe_tributos", type="decimal", precision=16, scale=2)
     */
    private $importeTributos = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones = '';

    /**
     * @var int
     *
     * @ORM\Column(name="obra_social_id", type="integer", nullable=true)
     */
    private $obraSocialId;

    /**
     * @var int
     *
     * @ORM\Column(name="obra_social_plan_id", type="integer", nullable=true)
     */
    private $obraSocialPlanId;

    //totalCosto = sumatoria de los articulos a precio costo (compra o venta)
    /**
     * @var string
     *
     * @ORM\Column(name="total_costo", type="decimal", precision=16, scale=2)
     */
    private $totalCosto = '0';

    //totalGanancia = total - totalCosto 
    /**
     * @var string
     *
     * @ORM\Column(name="total_ganancia", type="decimal", precision=16, scale=2)
     */
    private $totalGanancia = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cae_fecha_vencimiento", type="datetime", nullable=true)
     */
    private $caeFechaVencimiento;

    /**
     * @var bigint
     *
     * @ORM\Column(name="cae_numero", type="bigint", nullable=true)
     */
    private $caeNumero;

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
     * @var ArrayCollection ComprobanteDetalle
     */
    protected $comprobanteDetalles;

    public function __construct()
    {
        $this->comprobanteDetalles = new ArrayCollection();
    }
    
    public function getComprobanteDetalles()
    {
        if (is_null($this->comprobanteDetalles)) {
            $this->comprobanteDetalles = new ArrayCollection();
        }
        return $this->comprobanteDetalles;
    }

    /*
    public function setComprobanteDetalles(ArrayCollection $comprobanteDetalles)
    {
        $this->comprobanteDetalles = $comprobanteDetalles;
    }
    */
    
    public function addComprobanteDetalle(ComprobanteDetalle $comprobanteDetalle)
    {
        $this->comprobanteDetalles->add($comprobanteDetalle);
    }

    public function removeComprobanteDetalle(ComprobanteDetalle $comprobanteDetalle)
    {
        $this->comprobanteDetalles->remove($comprobanteDetalle);
    }

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
     * @return Comprobante
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Comprobante
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

    /**
     * Set puntoVenta
     *
     * @param integer $puntoVenta
     *
     * @return Comprobante
     */
    public function setPuntoVenta($puntoVenta)
    {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return integer
     */
    public function getPuntoVenta()
    {
        return $this->puntoVenta;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Comprobante
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set afipNumero
     *
     * @param integer $afipNumero
     *
     * @return Comprobante
     */
    public function setAfipNumero($afipNumero)
    {
        $this->afipNumero = $afipNumero;

        return $this;
    }

    /**
     * Get afipNumero
     *
     * @return integer
     */
    public function getAfipNumero()
    {
        return $this->afipNumero;
    }

    /**
     * Set totalBonificacion
     *
     * @param string $totalBonificacion
     *
     * @return Comprobante
     */
    public function setTotalBonificacion($totalBonificacion)
    {
        $this->totalBonificacion = $totalBonificacion;

        return $this;
    }

    /**
     * Get totalBonificacion
     *
     * @return string
     */
    public function getTotalBonificacion()
    {
        return $this->totalBonificacion;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * Set obraSocialId
     *
     * @param integer $obraSocialId
     *
     * @return Comprobante
     */
    public function setObraSocialId($obraSocialId)
    {
        $this->obraSocialId = $obraSocialId;

        return $this;
    }

    /**
     * Get obraSocialId
     *
     * @return integer
     */
    public function getObraSocialId()
    {
        return $this->obraSocialId;
    }

    /**
     * Set obraSocialPlanId
     *
     * @param integer $obraSocialPlanId
     *
     * @return Comprobante
     */
    public function setObraSocialPlanId($obraSocialPlanId)
    {
        $this->obraSocialPlanId = $obraSocialPlanId;

        return $this;
    }

    /**
     * Get obraSocialPlanId
     *
     * @return integer
     */
    public function getObraSocialPlanId()
    {
        return $this->obraSocialPlanId;
    }

    /**
     * Set totalCosto
     *
     * @param string $totalCosto
     *
     * @return Comprobante
     */
    public function setTotalCosto($totalCosto)
    {
        $this->totalCosto = $totalCosto;

        return $this;
    }

    /**
     * Get totalCosto
     *
     * @return string
     */
    public function getTotalCosto()
    {
        return $this->totalCosto;
    }

    /**
     * Set totalGanancia
     *
     * @param string $totalGanancia
     *
     * @return Comprobante
     */
    public function setTotalGanancia($totalGanancia)
    {
        $this->totalGanancia = $totalGanancia;

        return $this;
    }

    /**
     * Get totalGanancia
     *
     * @return string
     */
    public function getTotalGanancia()
    {
        return $this->totalGanancia;
    }

    /**
     * Set caeFechaVencimiento
     *
     * @param \DateTime $caeFechaVencimiento
     *
     * @return Comprobante
     */
    public function setCaeFechaVencimiento($caeFechaVencimiento)
    {
        $this->caeFechaVencimiento = $caeFechaVencimiento;

        return $this;
    }

    /**
     * Get caeFechaVencimiento
     *
     * @return \DateTime
     */
    public function getCaeFechaVencimiento()
    {
        return $this->caeFechaVencimiento;
    }

    /**
     * Set caeNumero
     *
     * @param integer $caeNumero
     *
     * @return Comprobante
     */
    public function setCaeNumero($caeNumero)
    {
        $this->caeNumero = $caeNumero;

        return $this;
    }

    /**
     * Get caeNumero
     *
     * @return integer
     */
    public function getCaeNumero()
    {
        return $this->caeNumero;
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * Set proveedor
     *
     * @param \AppBundle\Entity\Proveedor $proveedor
     *
     * @return Comprobante
     */
    public function setProveedor(\AppBundle\Entity\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \AppBundle\Entity\Proveedor
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return Comprobante
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set tipo
     *
     * @param \AppBundle\Entity\AfipComprobanteTipo $tipo
     *
     * @return Comprobante
     */
    public function setTipo(\AppBundle\Entity\AfipComprobanteTipo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \AppBundle\Entity\AfipComprobanteTipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set condicionVenta
     *
     * @param \AppBundle\Entity\AfipCondicionVenta $condicionVenta
     *
     * @return Comprobante
     */
    public function setCondicionVenta(\AppBundle\Entity\AfipCondicionVenta $condicionVenta = null)
    {
        $this->condicionVenta = $condicionVenta;

        return $this;
    }

    /**
     * Get condicionVenta
     *
     * @return \AppBundle\Entity\AfipCondicionVenta
     */
    public function getCondicionVenta()
    {
        return $this->condicionVenta;
    }
}
