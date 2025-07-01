<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrdenTrabajo
 *
 * @ORM\Table(name="orden_trabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenTrabajoRepository")
 */
class OrdenTrabajo
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
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * })
     */
    private $cliente;

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
     * @var \Medico
     *
     * @ORM\ManyToOne(targetEntity="Medico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medico_id", referencedColumnName="id")
     * })
     */
    private $medico;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_recepcion", type="datetime", nullable=true)
     */
    private $fechaRecepcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $comprobante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_receta", type="datetime", nullable=true)
     */
    private $fechaReceta;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @var \Taller
     *
     * @ORM\ManyToOne(targetEntity="Taller")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taller_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $taller;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_taller_pedido", type="datetime", nullable=true)
     */
    private $fechaTallerPedido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_talle_entrega", type="datetime", nullable=true)
     */
    private $fechaTallerEntrega;

    /**
     * @var bool
     *
     * @ORM\Column(name="armado", type="boolean")
     */
    private $armado;
    /**
     * @var bool
     *
     * @ORM\Column(name="armazon_propio", type="boolean")
     */
    private $armazon_propio;
    /**
     * @var string
     *
     * @ORM\Column(name="numero_taller", type="string", length=255, nullable=true)
     */
    private $numeroTaller = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="otros_trabajos", type="string", length=255, nullable=true)
     */
    private $otrosTrabajos;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    private $total = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="total_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $totalBonificacion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="entrega", type="decimal", precision=16, scale=2)
     */
    private $entrega = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="decimal", precision=16, scale=2)
     */
    private $saldo = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_derecho_eje", type="integer")
     */
    private $lejosOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_eje", type="integer")
     */
    private $lejosOjoIzquierdoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $lejosOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $lejosOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $lejosOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $lejosOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_derecho_eje", type="integer")
     */
    private $cercaOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_eje", type="integer")
     */
    private $cercaOjoIzquierdoEje = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $cercaOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $cercaOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $cercaOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $cercaOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_dnp", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoDnp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_dnp", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoDnp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_hp", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoHp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_hp", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoHp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_hpu", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoHpu = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_hpu", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoHpu = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_derecho_eje", type="integer")
     */
    private $antesLejosOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_izquierdo_eje", type="integer")
     */
    private $antesLejosOjoIzquierdoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $antesLejosOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $antesLejosOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $antesLejosOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $antesLejosOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_derecho_eje", type="integer")
     */
    private $antesCercaOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_izquierdo_eje", type="integer")
     */
    private $antesCercaOjoIzquierdoEje = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="antes_cerca_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $antesCercaOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $antesCercaOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $antesCercaOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $antesCercaOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_derecho_dnp", type="decimal", precision=16, scale=2)
     */
    private $antesOjoDerechoDnp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_izquierdo_dnp", type="decimal", precision=16, scale=2)
     */
    private $antesOjoIzquierdoDnp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_derecho_hp", type="decimal", precision=16, scale=2)
     */
    private $antesOjoDerechoHp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_izquierdo_hp", type="decimal", precision=16, scale=2)
     */
    private $antesOjoIzquierdoHp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_derecho_hpu", type="decimal", precision=16, scale=2)
     */
    private $antesOjoDerechoHpu = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_ojo_izquierdo_hpu", type="decimal", precision=16, scale=2)
     */
    private $antesOjoIzquierdoHpu = '0';

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
     * @var ArrayCollection OrdenTrabajoDetalle
     */
    protected $ordenTrabajoDetalles;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

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
        $this->ordenTrabajoDetalles = new ArrayCollection();
    }
    
    public function getOrdenTrabajoDetalles()
    {
        if (is_null($this->ordenTrabajoDetalles)) {
            $this->ordenTrabajoDetalles = new ArrayCollection();
        }
        return $this->ordenTrabajoDetalles;
    }

    /*
    public function setOrdenTrabajoDetalles(ArrayCollection $ordenTrabajoDetalles)
    {
        $this->ordenTrabajoDetalles = $ordenTrabajoDetalles;
    }
    */
    
    public function addOrdenTrabajoDetalle(OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        $this->ordenTrabajoDetalles->add($ordenTrabajoDetalle);
    }

    public function removeOrdenTrabajoDetalle(OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        $this->ordenTrabajoDetalles->remove($ordenTrabajoDetalle);
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
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return OrdenTrabajo
     */
    public function setFechaRecepcion($fechaRecepcion)
    {
        $this->fechaRecepcion = $fechaRecepcion;

        return $this;
    }

    /**
     * Get fechaRecepcion
     *
     * @return \DateTime
     */
    public function getFechaRecepcion()
    {
        return $this->fechaRecepcion;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     *
     * @return OrdenTrabajo
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set fechaReceta
     *
     * @param \DateTime $fechaReceta
     *
     * @return OrdenTrabajo
     */
    public function setFechaReceta($fechaReceta)
    {
        $this->fechaReceta = $fechaReceta;

        return $this;
    }

    /**
     * Get fechaReceta
     *
     * @return \DateTime
     */
    public function getFechaReceta()
    {
        return $this->fechaReceta;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return OrdenTrabajo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return OrdenTrabajo
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
     * Set fechaTallerPedido
     *
     * @param \DateTime $fechaTallerPedido
     *
     * @return OrdenTrabajo
     */
    public function setFechaTallerPedido($fechaTallerPedido)
    {
        $this->fechaTallerPedido = $fechaTallerPedido;

        return $this;
    }

    /**
     * Get fechaTallerPedido
     *
     * @return \DateTime
     */
    public function getFechaTallerPedido()
    {
        return $this->fechaTallerPedido;
    }

    /**
     * Set fechaTallerEntrega
     *
     * @param \DateTime $fechaTallerEntrega
     *
     * @return OrdenTrabajo
     */
    public function setFechaTallerEntrega($fechaTallerEntrega)
    {
        $this->fechaTallerEntrega = $fechaTallerEntrega;

        return $this;
    }

    /**
     * Get fechaTallerEntrega
     *
     * @return \DateTime
     */
    public function getFechaTallerEntrega()
    {
        return $this->fechaTallerEntrega;
    }

    /**
     * Set armado
     *
     * @param boolean $armado
     *
     * @return OrdenTrabajo
     */
    public function setArmado($armado)
    {
        $this->armado = $armado;

        return $this;
    }

    /**
     * Get armado
     *
     * @return boolean
     */
    public function getArmado()
    {
        return $this->armado;
    }
    /**
     * Set armazon_propio
     *
     * @param boolean $armazon_propio
     *
     * @return OrdenTrabajo
     */
    public function setArmazonPropio($armazon_propio)
    {
        $this->armazon_propio = $armazon_propio;

        return $this;
    }

    /**
     * Get armazon_propio
     *
     * @return boolean
     */
    public function getArmazonPropio()
    {
        return $this->armazon_propio;
    }
    /**
     * Set numeroTaller
     *
     * @param integer $numeroTaller
     *
     * @return OrdenTrabajo
     */
    public function setNumeroTaller($numeroTaller)
    {
        $this->numeroTaller = $numeroTaller;

        return $this;
    }

    /**
     * Get numeroTaller
     *
     * @return integer
     */
    public function getNumeroTaller()
    {
        return $this->numeroTaller;
    }

    /**
     * Set otrosTrabajos
     *
     * @param string $otrosTrabajos
     *
     * @return OrdenTrabajo
     */
    public function setOtrosTrabajos($otrosTrabajos)
    {
        $this->otrosTrabajos = $otrosTrabajos;

        return $this;
    }

    /**
     * Get otrosTrabajos
     *
     * @return string
     */
    public function getOtrosTrabajos()
    {
        return $this->otrosTrabajos;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return OrdenTrabajo
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
     * Set totalBonificacion
     *
     * @param string $totalBonificacion
     *
     * @return OrdenTrabajo
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
     * Set entrega
     *
     * @param string $entrega
     *
     * @return OrdenTrabajo
     */
    public function setEntrega($entrega)
    {
        $this->entrega = $entrega;

        return $this;
    }

    /**
     * Get entrega
     *
     * @return string
     */
    public function getEntrega()
    {
        return $this->entrega;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     *
     * @return OrdenTrabajo
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
     * Set lejosOjoDerechoEje
     *
     * @param integer $lejosOjoDerechoEje
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoDerechoEje($lejosOjoDerechoEje)
    {
        $this->lejosOjoDerechoEje = $lejosOjoDerechoEje;

        return $this;
    }

    /**
     * Get lejosOjoDerechoEje
     *
     * @return integer
     */
    public function getLejosOjoDerechoEje()
    {
        return $this->lejosOjoDerechoEje;
    }

    /**
     * Set lejosOjoIzquierdoEje
     *
     * @param integer $lejosOjoIzquierdoEje
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoIzquierdoEje($lejosOjoIzquierdoEje)
    {
        $this->lejosOjoIzquierdoEje = $lejosOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoEje
     *
     * @return integer
     */
    public function getLejosOjoIzquierdoEje()
    {
        return $this->lejosOjoIzquierdoEje;
    }

    /**
     * Set lejosOjoDerechoCilindro
     *
     * @param string $lejosOjoDerechoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoDerechoCilindro($lejosOjoDerechoCilindro)
    {
        $this->lejosOjoDerechoCilindro = $lejosOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get lejosOjoDerechoCilindro
     *
     * @return string
     */
    public function getLejosOjoDerechoCilindro()
    {
        return $this->lejosOjoDerechoCilindro;
    }

    /**
     * Set lejosOjoIzquierdoCilindro
     *
     * @param string $lejosOjoIzquierdoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoIzquierdoCilindro($lejosOjoIzquierdoCilindro)
    {
        $this->lejosOjoIzquierdoCilindro = $lejosOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getLejosOjoIzquierdoCilindro()
    {
        return $this->lejosOjoIzquierdoCilindro;
    }

    /**
     * Set lejosOjoDerechoEsfera
     *
     * @param string $lejosOjoDerechoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoDerechoEsfera($lejosOjoDerechoEsfera)
    {
        $this->lejosOjoDerechoEsfera = $lejosOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get lejosOjoDerechoEsfera
     *
     * @return string
     */
    public function getLejosOjoDerechoEsfera()
    {
        return $this->lejosOjoDerechoEsfera;
    }

    /**
     * Set lejosOjoIzquierdoEsfera
     *
     * @param string $lejosOjoIzquierdoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setLejosOjoIzquierdoEsfera($lejosOjoIzquierdoEsfera)
    {
        $this->lejosOjoIzquierdoEsfera = $lejosOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getLejosOjoIzquierdoEsfera()
    {
        return $this->lejosOjoIzquierdoEsfera;
    }

    /**
     * Set cercaOjoDerechoEje
     *
     * @param integer $cercaOjoDerechoEje
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoDerechoEje($cercaOjoDerechoEje)
    {
        $this->cercaOjoDerechoEje = $cercaOjoDerechoEje;

        return $this;
    }

    /**
     * Get cercaOjoDerechoEje
     *
     * @return integer
     */
    public function getCercaOjoDerechoEje()
    {
        return $this->cercaOjoDerechoEje;
    }

    /**
     * Set cercaOjoIzquierdoEje
     *
     * @param integer $cercaOjoIzquierdoEje
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoIzquierdoEje($cercaOjoIzquierdoEje)
    {
        $this->cercaOjoIzquierdoEje = $cercaOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoEje
     *
     * @return integer
     */
    public function getCercaOjoIzquierdoEje()
    {
        return $this->cercaOjoIzquierdoEje;
    }

    /**
     * Set cercaOjoDerechoCilindro
     *
     * @param string $cercaOjoDerechoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoDerechoCilindro($cercaOjoDerechoCilindro)
    {
        $this->cercaOjoDerechoCilindro = $cercaOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get cercaOjoDerechoCilindro
     *
     * @return string
     */
    public function getCercaOjoDerechoCilindro()
    {
        return $this->cercaOjoDerechoCilindro;
    }

    /**
     * Set cercaOjoIzquierdoCilindro
     *
     * @param string $cercaOjoIzquierdoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoIzquierdoCilindro($cercaOjoIzquierdoCilindro)
    {
        $this->cercaOjoIzquierdoCilindro = $cercaOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getCercaOjoIzquierdoCilindro()
    {
        return $this->cercaOjoIzquierdoCilindro;
    }

    /**
     * Set cercaOjoDerechoEsfera
     *
     * @param string $cercaOjoDerechoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoDerechoEsfera($cercaOjoDerechoEsfera)
    {
        $this->cercaOjoDerechoEsfera = $cercaOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get cercaOjoDerechoEsfera
     *
     * @return string
     */
    public function getCercaOjoDerechoEsfera()
    {
        return $this->cercaOjoDerechoEsfera;
    }

    /**
     * Set cercaOjoIzquierdoEsfera
     *
     * @param string $cercaOjoIzquierdoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setCercaOjoIzquierdoEsfera($cercaOjoIzquierdoEsfera)
    {
        $this->cercaOjoIzquierdoEsfera = $cercaOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getCercaOjoIzquierdoEsfera()
    {
        return $this->cercaOjoIzquierdoEsfera;
    }

    /**
     * Set ojoDerechoDnp
     *
     * @param string $ojoDerechoDnp
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoDnp($ojoDerechoDnp)
    {
        $this->ojoDerechoDnp = $ojoDerechoDnp;

        return $this;
    }

    /**
     * Get ojoDerechoDnp
     *
     * @return string
     */
    public function getOjoDerechoDnp()
    {
        return $this->ojoDerechoDnp;
    }

    /**
     * Set ojoIzquierdoDnp
     *
     * @param string $ojoIzquierdoDnp
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoDnp($ojoIzquierdoDnp)
    {
        $this->ojoIzquierdoDnp = $ojoIzquierdoDnp;

        return $this;
    }

    /**
     * Get ojoIzquierdoDnp
     *
     * @return string
     */
    public function getOjoIzquierdoDnp()
    {
        return $this->ojoIzquierdoDnp;
    }

    /**
     * Set ojoDerechoHp
     *
     * @param string $ojoDerechoHp
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoHp($ojoDerechoHp)
    {
        $this->ojoDerechoHp = $ojoDerechoHp;

        return $this;
    }

    /**
     * Get ojoDerechoHp
     *
     * @return string
     */
    public function getOjoDerechoHp()
    {
        return $this->ojoDerechoHp;
    }

    /**
     * Set ojoIzquierdoHp
     *
     * @param string $ojoIzquierdoHp
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoHp($ojoIzquierdoHp)
    {
        $this->ojoIzquierdoHp = $ojoIzquierdoHp;

        return $this;
    }

    /**
     * Get ojoIzquierdoHp
     *
     * @return string
     */
    public function getOjoIzquierdoHp()
    {
        return $this->ojoIzquierdoHp;
    }

    /**
     * Set ojoDerechoHpu
     *
     * @param string $ojoDerechoHpu
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoHpu($ojoDerechoHpu)
    {
        $this->ojoDerechoHpu = $ojoDerechoHpu;

        return $this;
    }

    /**
     * Get ojoDerechoHpu
     *
     * @return string
     */
    public function getOjoDerechoHpu()
    {
        return $this->ojoDerechoHpu;
    }

    /**
     * Set ojoIzquierdoHpu
     *
     * @param string $ojoIzquierdoHpu
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoHpu($ojoIzquierdoHpu)
    {
        $this->ojoIzquierdoHpu = $ojoIzquierdoHpu;

        return $this;
    }

    /**
     * Get ojoIzquierdoHpu
     *
     * @return string
     */
    public function getOjoIzquierdoHpu()
    {
        return $this->ojoIzquierdoHpu;
    }

    /**
     * Set antesLejosOjoDerechoEje
     *
     * @param integer $antesLejosOjoDerechoEje
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoDerechoEje($antesLejosOjoDerechoEje)
    {
        $this->antesLejosOjoDerechoEje = $antesLejosOjoDerechoEje;

        return $this;
    }

    /**
     * Get antesLejosOjoDerechoEje
     *
     * @return integer
     */
    public function getAntesLejosOjoDerechoEje()
    {
        return $this->antesLejosOjoDerechoEje;
    }

    /**
     * Set antesLejosOjoIzquierdoEje
     *
     * @param integer $antesLejosOjoIzquierdoEje
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoIzquierdoEje($antesLejosOjoIzquierdoEje)
    {
        $this->antesLejosOjoIzquierdoEje = $antesLejosOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get antesLejosOjoIzquierdoEje
     *
     * @return integer
     */
    public function getAntesLejosOjoIzquierdoEje()
    {
        return $this->antesLejosOjoIzquierdoEje;
    }

    /**
     * Set antesLejosOjoDerechoCilindro
     *
     * @param string $antesLejosOjoDerechoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoDerechoCilindro($antesLejosOjoDerechoCilindro)
    {
        $this->antesLejosOjoDerechoCilindro = $antesLejosOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get antesLejosOjoDerechoCilindro
     *
     * @return string
     */
    public function getAntesLejosOjoDerechoCilindro()
    {
        return $this->antesLejosOjoDerechoCilindro;
    }

    /**
     * Set antesLejosOjoIzquierdoCilindro
     *
     * @param string $antesLejosOjoIzquierdoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoIzquierdoCilindro($antesLejosOjoIzquierdoCilindro)
    {
        $this->antesLejosOjoIzquierdoCilindro = $antesLejosOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get antesLejosOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getAntesLejosOjoIzquierdoCilindro()
    {
        return $this->antesLejosOjoIzquierdoCilindro;
    }

    /**
     * Set antesLejosOjoDerechoEsfera
     *
     * @param string $antesLejosOjoDerechoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoDerechoEsfera($antesLejosOjoDerechoEsfera)
    {
        $this->antesLejosOjoDerechoEsfera = $antesLejosOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get antesLejosOjoDerechoEsfera
     *
     * @return string
     */
    public function getAntesLejosOjoDerechoEsfera()
    {
        return $this->antesLejosOjoDerechoEsfera;
    }

    /**
     * Set antesLejosOjoIzquierdoEsfera
     *
     * @param string $antesLejosOjoIzquierdoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setAntesLejosOjoIzquierdoEsfera($antesLejosOjoIzquierdoEsfera)
    {
        $this->antesLejosOjoIzquierdoEsfera = $antesLejosOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get antesLejosOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getAntesLejosOjoIzquierdoEsfera()
    {
        return $this->antesLejosOjoIzquierdoEsfera;
    }

    /**
     * Set antesCercaOjoDerechoEje
     *
     * @param integer $antesCercaOjoDerechoEje
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoDerechoEje($antesCercaOjoDerechoEje)
    {
        $this->antesCercaOjoDerechoEje = $antesCercaOjoDerechoEje;

        return $this;
    }

    /**
     * Get antesCercaOjoDerechoEje
     *
     * @return integer
     */
    public function getAntesCercaOjoDerechoEje()
    {
        return $this->antesCercaOjoDerechoEje;
    }

    /**
     * Set antesCercaOjoIzquierdoEje
     *
     * @param integer $antesCercaOjoIzquierdoEje
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoIzquierdoEje($antesCercaOjoIzquierdoEje)
    {
        $this->antesCercaOjoIzquierdoEje = $antesCercaOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get antesCercaOjoIzquierdoEje
     *
     * @return integer
     */
    public function getAntesCercaOjoIzquierdoEje()
    {
        return $this->antesCercaOjoIzquierdoEje;
    }

    /**
     * Set antesCercaOjoDerechoCilindro
     *
     * @param string $antesCercaOjoDerechoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoDerechoCilindro($antesCercaOjoDerechoCilindro)
    {
        $this->antesCercaOjoDerechoCilindro = $antesCercaOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get antesCercaOjoDerechoCilindro
     *
     * @return string
     */
    public function getAntesCercaOjoDerechoCilindro()
    {
        return $this->antesCercaOjoDerechoCilindro;
    }

    /**
     * Set antesCercaOjoIzquierdoCilindro
     *
     * @param string $antesCercaOjoIzquierdoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoIzquierdoCilindro($antesCercaOjoIzquierdoCilindro)
    {
        $this->antesCercaOjoIzquierdoCilindro = $antesCercaOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get antesCercaOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getAntesCercaOjoIzquierdoCilindro()
    {
        return $this->antesCercaOjoIzquierdoCilindro;
    }

    /**
     * Set antesCercaOjoDerechoEsfera
     *
     * @param string $antesCercaOjoDerechoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoDerechoEsfera($antesCercaOjoDerechoEsfera)
    {
        $this->antesCercaOjoDerechoEsfera = $antesCercaOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get antesCercaOjoDerechoEsfera
     *
     * @return string
     */
    public function getAntesCercaOjoDerechoEsfera()
    {
        return $this->antesCercaOjoDerechoEsfera;
    }

    /**
     * Set antesCercaOjoIzquierdoEsfera
     *
     * @param string $antesCercaOjoIzquierdoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setAntesCercaOjoIzquierdoEsfera($antesCercaOjoIzquierdoEsfera)
    {
        $this->antesCercaOjoIzquierdoEsfera = $antesCercaOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get antesCercaOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getAntesCercaOjoIzquierdoEsfera()
    {
        return $this->antesCercaOjoIzquierdoEsfera;
    }

    /**
     * Set antesOjoDerechoDnp
     *
     * @param string $antesOjoDerechoDnp
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoDerechoDnp($antesOjoDerechoDnp)
    {
        $this->antesOjoDerechoDnp = $antesOjoDerechoDnp;

        return $this;
    }

    /**
     * Get antesOjoDerechoDnp
     *
     * @return string
     */
    public function getAntesOjoDerechoDnp()
    {
        return $this->antesOjoDerechoDnp;
    }

    /**
     * Set antesOjoIzquierdoDnp
     *
     * @param string $antesOjoIzquierdoDnp
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoIzquierdoDnp($antesOjoIzquierdoDnp)
    {
        $this->antesOjoIzquierdoDnp = $antesOjoIzquierdoDnp;

        return $this;
    }

    /**
     * Get antesOjoIzquierdoDnp
     *
     * @return string
     */
    public function getAntesOjoIzquierdoDnp()
    {
        return $this->antesOjoIzquierdoDnp;
    }

    /**
     * Set antesOjoDerechoHp
     *
     * @param string $antesOjoDerechoHp
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoDerechoHp($antesOjoDerechoHp)
    {
        $this->antesOjoDerechoHp = $antesOjoDerechoHp;

        return $this;
    }

    /**
     * Get antesOjoDerechoHp
     *
     * @return string
     */
    public function getAntesOjoDerechoHp()
    {
        return $this->antesOjoDerechoHp;
    }

    /**
     * Set antesOjoIzquierdoHp
     *
     * @param string $antesOjoIzquierdoHp
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoIzquierdoHp($antesOjoIzquierdoHp)
    {
        $this->antesOjoIzquierdoHp = $antesOjoIzquierdoHp;

        return $this;
    }

    /**
     * Get antesOjoIzquierdoHp
     *
     * @return string
     */
    public function getAntesOjoIzquierdoHp()
    {
        return $this->antesOjoIzquierdoHp;
    }

    /**
     * Set antesOjoDerechoHpu
     *
     * @param string $antesOjoDerechoHpu
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoDerechoHpu($antesOjoDerechoHpu)
    {
        $this->antesOjoDerechoHpu = $antesOjoDerechoHpu;

        return $this;
    }

    /**
     * Get antesOjoDerechoHpu
     *
     * @return string
     */
    public function getAntesOjoDerechoHpu()
    {
        return $this->antesOjoDerechoHpu;
    }

    /**
     * Set antesOjoIzquierdoHpu
     *
     * @param string $antesOjoIzquierdoHpu
     *
     * @return OrdenTrabajo
     */
    public function setAntesOjoIzquierdoHpu($antesOjoIzquierdoHpu)
    {
        $this->antesOjoIzquierdoHpu = $antesOjoIzquierdoHpu;

        return $this;
    }

    /**
     * Get antesOjoIzquierdoHpu
     *
     * @return string
     */
    public function getAntesOjoIzquierdoHpu()
    {
        return $this->antesOjoIzquierdoHpu;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrdenTrabajo
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
     * @return OrdenTrabajo
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
     * Set activo
     *
     * @param boolean $activo
     *
     * @return OrdenTrabajo
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
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return OrdenTrabajo
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
     * Set obraSocialPlan
     *
     * @param \AppBundle\Entity\ObraSocialPlan $obraSocialPlan
     *
     * @return OrdenTrabajo
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
     * Set medico
     *
     * @param \AppBundle\Entity\Medico $medico
     *
     * @return OrdenTrabajo
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

    /**
     * Set comprobante
     *
     * @param \AppBundle\Entity\Comprobante $comprobante
     *
     * @return OrdenTrabajo
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
     * Set taller
     *
     * @param \AppBundle\Entity\Taller $taller
     *
     * @return OrdenTrabajo
     */
    public function setTaller(\AppBundle\Entity\Taller $taller = null)
    {
        $this->taller = $taller;

        return $this;
    }

    /**
     * Get taller
     *
     * @return \AppBundle\Entity\Taller
     */
    public function getTaller()
    {
        return $this->taller;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return OrdenTrabajo
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return OrdenTrabajo
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
     * @return OrdenTrabajo
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
     * Set sucursal
     *
     * @param \AppBundle\Entity\Sucursal $sucursal
     *
     * @return OrdenTrabajo
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
}
