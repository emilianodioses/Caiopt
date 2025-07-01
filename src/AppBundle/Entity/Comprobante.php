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

    /**
     * @var \OrdenTrabajo
     *
     * @ORM\ManyToOne(targetEntity="OrdenTrabajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ordentrabajo_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $ordenTrabajo;

    /**
     * @var \OrdenTrabajo
     *
     * @ORM\ManyToOne(targetEntity="OrdenTrabajoContactologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ordentrabajocontactologia_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $ordenTrabajoContactologia;

    /**
     * @var \Medico
     *
     * @ORM\ManyToOne(targetEntity="Medico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medico_id", referencedColumnName="id")
     * })
     */
    private $medico;

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

    /**
     * @var \PuntoVenta
     *
     * @ORM\ManyToOne(targetEntity="PuntoVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="punto_venta_id", referencedColumnName="id")
     * })
     */
    private $puntoVentaId;

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

    //ClienteRazonSocial = Nombre del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_razon_social", type="string", length=255)
     */
    private $clienteRazonSocial = '';

    //ClienteDocumentoTipo = Tipo de documento del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_documento_tipo", type="string", length=255)
     */
    private $clienteDocumentoTipo = '';

    //ClienteDocumentoNumero = Numero de documento del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_documento_numero", type="string", length=255)
     */
    private $clienteDocumentoNumero = '';

    //ClienteDomicilio = Domicilio del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_domicilio", type="string", length=255)
     */
    private $clienteDomicilio = '';

    //ClienteLocalidad = Localidad del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_localidad", type="string", length=255)
     */
    private $clienteLocalidad = '';

    //ClienteIvaCondicion = Condicion de IVA del cliente al momento de hacer la factura
    /**
     * @var string
     *
     * @ORM\Column(name="cliente_iva_condicion", type="string", length=255)
     */
    private $clienteIvaCondicion = '';

    /**
     * @var \ObraSocial
     *
     * @ORM\ManyToOne(targetEntity="ObraSocial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="obra_social_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $obraSocial;

    /**
     * @var \ObraSocialPlan
     *
     * @ORM\ManyToOne(targetEntity="ObraSocialPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="obra_social_plan_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $obraSocialPlan;

    //pendiente = importe que queda por pagar/cobrar del comprobante
    /**
     * @var string
     *
     * @ORM\Column(name="pendiente", type="decimal", precision=16, scale=2)
     */
    private $pendiente;

    //saldo = importe de la cuenta del cliente/proveedor luego del movimiento asentado en este comprobante
    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="decimal", precision=16, scale=2)
     */
    private $saldo;

    /**
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante_asociado_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $comprobanteAsociado;

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
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    
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
     * @var ArrayCollection ComprobanteDetalle
     */
    protected $comprobanteDetalles;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id")
     * })
     */
    private $sucursal;

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

    public function __toString()
    {
        if ($this->movimiento == 'Venta') {
            return $this->tipo->getDescripcionCorta().': '.$this->puntoVenta.'-'.$this->afipNumero.'(N° Int: '.$this->numero.')';
        }
        else {
            return $this->tipo->getDescripcionCorta().': '.$this->puntoVenta.'-'.$this->numero;
        }
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

    /**
     * Set obraSocial
     *
     * @param \AppBundle\Entity\ObraSocial $obraSocial
     *
     * @return Comprobante
     */
    public function setObraSocial(\AppBundle\Entity\ObraSocial $obraSocial = null)
    {
        $this->obraSocial = $obraSocial;

        return $this;
    }

    /**
     * Get obraSocial
     *
     * @return \AppBundle\Entity\ObraSocial
     */
    public function getObraSocial()
    {
        return $this->obraSocial;
    }

    /**
     * Set obraSocialPlan
     *
     * @param \AppBundle\Entity\ObraSocialPlan $obraSocialPlan
     *
     * @return Comprobante
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

    /**
     * Set sucursal
     *
     * @param \AppBundle\Entity\Sucursal $sucursal
     *
     * @return Comprobante
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
     * Set ordenTrabajo
     *
     * @param \AppBundle\Entity\OrdenTrabajo $ordenTrabajo
     *
     * @return Comprobante
     */
    public function setOrdenTrabajo(\AppBundle\Entity\OrdenTrabajo $ordenTrabajo = null)
    {
        $this->ordenTrabajo = $ordenTrabajo;

        return $this;
    }

    /**
     * Get ordenTrabajo
     *
     * @return \AppBundle\Entity\OrdenTrabajo
     */
    public function getOrdenTrabajo()
    {
        return $this->ordenTrabajo;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     *
     * @return Comprobante
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
     * Set pendiente
     *
     * @param string $pendiente
     *
     * @return Comprobante
     */
    public function setPendiente($pendiente)
    {
        $this->pendiente = $pendiente;

        return $this;
    }

    /**
     * Get pendiente
     *
     * @return string
     */
    public function getPendiente()
    {
        return $this->pendiente;
    }

    /**
     * Set ordenTrabajoContactologia
     *
     * @param \AppBundle\Entity\OrdenTrabajoContactologia $ordenTrabajoContactologia
     *
     * @return Comprobante
     */
    public function setOrdenTrabajoContactologia(\AppBundle\Entity\OrdenTrabajoContactologia $ordenTrabajoContactologia = null)
    {
        $this->ordenTrabajoContactologia = $ordenTrabajoContactologia;

        return $this;
    }

    /**
     * Get ordenTrabajoContactologia
     *
     * @return \AppBundle\Entity\OrdenTrabajoContactologia
     */
    public function getOrdenTrabajoContactologia()
    {
        return $this->ordenTrabajoContactologia;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return Comprobante
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
     * @return Comprobante
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
     * Set clienteRazonSocial
     *
     * @param string $clienteRazonSocial
     *
     * @return Comprobante
     */
    public function setClienteRazonSocial($clienteRazonSocial)
    {
        $this->clienteRazonSocial = $clienteRazonSocial;

        return $this;
    }

    /**
     * Get clienteRazonSocial
     *
     * @return string
     */
    public function getClienteRazonSocial()
    {
        return $this->clienteRazonSocial;
    }

    /**
     * Set clienteDocumentoTipo
     *
     * @param string $clienteDocumentoTipo
     *
     * @return Comprobante
     */
    public function setClienteDocumentoTipo($clienteDocumentoTipo)
    {
        $this->clienteDocumentoTipo = $clienteDocumentoTipo;

        return $this;
    }

    /**
     * Get clienteDocumentoTipo
     *
     * @return string
     */
    public function getClienteDocumentoTipo()
    {
        return $this->clienteDocumentoTipo;
    }

    /**
     * Set clienteDocumentoNumero
     *
     * @param string $clienteDocumentoNumero
     *
     * @return Comprobante
     */
    public function setClienteDocumentoNumero($clienteDocumentoNumero)
    {
        $this->clienteDocumentoNumero = $clienteDocumentoNumero;

        return $this;
    }

    /**
     * Get clienteDocumentoNumero
     *
     * @return string
     */
    public function getClienteDocumentoNumero()
    {
        return $this->clienteDocumentoNumero;
    }

    /**
     * Set clienteDomicilio
     *
     * @param string $clienteDomicilio
     *
     * @return Comprobante
     */
    public function setClienteDomicilio($clienteDomicilio)
    {
        $this->clienteDomicilio = $clienteDomicilio;

        return $this;
    }

    /**
     * Get clienteDomicilio
     *
     * @return string
     */
    public function getClienteDomicilio()
    {
        return $this->clienteDomicilio;
    }

    /**
     * Set clienteLocalidad
     *
     * @param string $clienteLocalidad
     *
     * @return Comprobante
     */
    public function setClienteLocalidad($clienteLocalidad)
    {
        $this->clienteLocalidad = $clienteLocalidad;

        return $this;
    }

    /**
     * Get clienteLocalidad
     *
     * @return string
     */
    public function getClienteLocalidad()
    {
        return $this->clienteLocalidad;
    }

    /**
     * Set clienteIvaCondicion
     *
     * @param string $clienteIvaCondicion
     *
     * @return Comprobante
     */
    public function setClienteIvaCondicion($clienteIvaCondicion)
    {
        $this->clienteIvaCondicion = $clienteIvaCondicion;

        return $this;
    }

    /**
     * Get clienteIvaCondicion
     *
     * @return string
     */
    public function getClienteIvaCondicion()
    {
        return $this->clienteIvaCondicion;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return Comprobante
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set comprobanteAsociado
     *
     * @param \AppBundle\Entity\Comprobante $comprobanteAsociado
     *
     * @return Comprobante
     */
    public function setComprobanteAsociado(\AppBundle\Entity\Comprobante $comprobanteAsociado = null)
    {
        $this->comprobanteAsociado = $comprobanteAsociado;

        return $this;
    }

    /**
     * Get comprobanteAsociado
     *
     * @return \AppBundle\Entity\Comprobante
     */
    public function getComprobanteAsociado()
    {
        return $this->comprobanteAsociado;
    }

    /**
     * Set puntoVentaId
     *
     * @param \AppBundle\Entity\PuntoVenta $puntoVentaId
     *
     * @return Comprobante
     */
    public function setPuntoVentaId(\AppBundle\Entity\PuntoVenta $puntoVentaId = null)
    {
        $this->puntoVentaId = $puntoVentaId;

        return $this;
    }

    /**
     * Get puntoVentaId
     *
     * @return \AppBundle\Entity\PuntoVenta
     */
    public function getPuntoVentaId()
    {
        return $this->puntoVentaId;
    }

    /**
     * Set medico
     *
     * @param \AppBundle\Entity\Medico $medico
     *
     * @return Comprobante
     */
    public function setMedico(\AppBundle\Entity\Medico $medico = null)
    {
        $this->medico = $medico;

        return $this;
    }

    /**
     * Get medico
     *
     * @return \AppBundle\Entity\Medico
     */
    public function getMedico()
    {
        return $this->medico;
    }
}
