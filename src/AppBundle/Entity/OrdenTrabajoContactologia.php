<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * OrdenTrabajoContactologia
 *
 * @ORM\Table(name="orden_trabajo_contactologia")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenTrabajoContactologiaRepository")
 */
class OrdenTrabajoContactologia
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
     * @ORM\Column(name="diagnostico", type="string", length=255, nullable=true)
     */
    private $diagnostico;

    /**
     * @var string
     *
     * @ORM\Column(name="rp", type="string", length=255, nullable=true)
     */
    private $rp;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="motivacion", type="string", length=255, nullable=true)
     */
    private $motivacion;

    /**
     * @var string
     *
     * @ORM\Column(name="uso_lc", type="string", length=255, nullable=true)
     */
    private $usoLC;

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
     * @ORM\Column(name="rc_ojo_derecho_horizontal", type="decimal", precision=16, scale=2)
     */
    private $rcOjoDerechoHorizontal = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rc_ojo_izquierdo_horizontal", type="decimal", precision=16, scale=2)
     */
    private $rcOjoIzquierdoHorizontal = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rc_ojo_derecho_vertical", type="decimal", precision=16, scale=2)
     */
    private $rcOjoDerechoVertical = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rc_ojo_izquierdo_vertical", type="decimal", precision=16, scale=2)
     */
    private $rcOjoIzquierdoVertical = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_curvas", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoCurvas = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_curvas", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoCurvas = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_diametro", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoDiametro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_diametro", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoDiametro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_av", type="decimal", precision=16, scale=2)
     */
    private $ojoDerechoAV = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_av", type="decimal", precision=16, scale=2)
     */
    private $ojoIzquierdoAV = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_derecho_caracteristicas", type="string", length=255, nullable=true)
     */
    private $ojoDerechoCaracteristicas;

    /**
     * @var string
     *
     * @ORM\Column(name="ojo_izquierdo_caracteristicas", type="string", length=255, nullable=true)
     */
    private $ojoIzquierdoCaracteristicas;

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $lejosOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
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
     * @ORM\Column(name="cerca_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $cercaOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
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
     * @ORM\Column(name="antes_lejos_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $antesLejosOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_lejos_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
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
     * @ORM\Column(name="antes_cerca_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $antesCercaOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="antes_cerca_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
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
     * @ORM\Column(name="final_ojo_izquierdo_dnp", type="decimal", precision=16, scale=2)
     */
    private $finalOjoIzquierdoDnp = '0';


    /**
     * @var string
     *
     * @ORM\Column(name="final_ojo_derecho_dnp", type="decimal", precision=16, scale=2)
     */
    private $finalOjoDerechoDnp = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoIzquierdoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_cerca_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $finalCercaOjoDerechoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_izquierdo_esfera", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoIzquierdoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_derecho_esfera", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoDerechoEsfera = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_izquierdo_cilindro", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoIzquierdoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_derecho_cilindro", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoDerechoCilindro = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_izquierdo_eje", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoIzquierdoEje = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="final_lejos_ojo_derecho_eje", type="decimal", precision=16, scale=2)
     */
    private $finalLejosOjoDerechoEje = '0';

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
     * @var ArrayCollection OrdenTrabajoContactologiaDetalle
     */
    protected $OrdenTrabajoContactologiaDetalles;

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
/*************************TALLER*******/
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
     * @var string
     *
     * @ORM\Column(name="numero_taller", type="string", length=255, nullable=true)
     */
    private $numeroTaller = '0';

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="otros_trabajos", type="string", length=255, nullable=true)
//     */
//    private $otrosTrabajos;
/*************************TALLER*******/
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->OrdenTrabajoContactologiaDetalles = new ArrayCollection();
    }
    
    public function getOrdenTrabajoContactologiaDetalles()
    {
        if (is_null($this->OrdenTrabajoContactologiaDetalles)) {
            $this->OrdenTrabajoContactologiaDetalles = new ArrayCollection();
        }
        return $this->OrdenTrabajoContactologiaDetalles;
    }

    /*
    public function setOrdenTrabajoContactologiaDetalles(ArrayCollection $OrdenTrabajoContactologiaDetalles)
    {
        $this->OrdenTrabajoContactologiaDetalles = $OrdenTrabajoContactologiaDetalles;
    }
    */
    
    public function addOrdenTrabajoContactologiaDetalle(OrdenTrabajoContactologiaDetalle $OrdenTrabajoContactologiaDetalle)
    {
        $this->OrdenTrabajoContactologiaDetalles->add($OrdenTrabajoContactologiaDetalle);
    }

    public function removeOrdenTrabajoContactologiaDetalle(OrdenTrabajoContactologiaDetalle $OrdenTrabajoContactologiaDetalle)
    {
        $this->OrdenTrabajoContactologiaDetalles->remove($OrdenTrabajoContactologiaDetalle);
    }


    /**
     * Set fechaRecepcion
     *
     * @param \DateTime $fechaRecepcion
     *
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set diagnostico
     *
     * @param string $diagnostico
     *
     * @return OrdenTrabajoContactologia
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    /**
     * Get diagnostico
     *
     * @return string
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * Set rp
     *
     * @param string $rp
     *
     * @return OrdenTrabajoContactologia
     */
    public function setRp($rp)
    {
        $this->rp = $rp;

        return $this;
    }

    /**
     * Get rp
     *
     * @return string
     */
    public function getRp()
    {
        return $this->rp;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return OrdenTrabajoContactologia
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
     * Set motivacion
     *
     * @param string $motivacion
     *
     * @return OrdenTrabajoContactologia
     */
    public function setMotivacion($motivacion)
    {
        $this->motivacion = $motivacion;

        return $this;
    }

    /**
     * Get motivacion
     *
     * @return string
     */
    public function getMotivacion()
    {
        return $this->motivacion;
    }

    /**
     * Set usoLC
     *
     * @param string $usoLC
     *
     * @return OrdenTrabajoContactologia
     */
    public function setUsoLC($usoLC)
    {
        $this->usoLC = $usoLC;

        return $this;
    }

    /**
     * Get usoLC
     *
     * @return string
     */
    public function getUsoLC()
    {
        return $this->usoLC;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set rcOjoDerechoHorizontal
     *
     * @param string $rcOjoDerechoHorizontal
     *
     * @return OrdenTrabajoContactologia
     */
    public function setRcOjoDerechoHorizontal($rcOjoDerechoHorizontal)
    {
        $this->rcOjoDerechoHorizontal = $rcOjoDerechoHorizontal;

        return $this;
    }

    /**
     * Get rcOjoDerechoHorizontal
     *
     * @return string
     */
    public function getRcOjoDerechoHorizontal()
    {
        return $this->rcOjoDerechoHorizontal;
    }

    /**
     * Set rcOjoIzquierdoHorizontal
     *
     * @param string $rcOjoIzquierdoHorizontal
     *
     * @return OrdenTrabajoContactologia
     */
    public function setRcOjoIzquierdoHorizontal($rcOjoIzquierdoHorizontal)
    {
        $this->rcOjoIzquierdoHorizontal = $rcOjoIzquierdoHorizontal;

        return $this;
    }

    /**
     * Get rcOjoIzquierdoHorizontal
     *
     * @return string
     */
    public function getRcOjoIzquierdoHorizontal()
    {
        return $this->rcOjoIzquierdoHorizontal;
    }

    /**
     * Set rcOjoDerechoVertical
     *
     * @param string $rcOjoDerechoVertical
     *
     * @return OrdenTrabajoContactologia
     */
    public function setRcOjoDerechoVertical($rcOjoDerechoVertical)
    {
        $this->rcOjoDerechoVertical = $rcOjoDerechoVertical;

        return $this;
    }

    /**
     * Get rcOjoDerechoVertical
     *
     * @return string
     */
    public function getRcOjoDerechoVertical()
    {
        return $this->rcOjoDerechoVertical;
    }

    /**
     * Set rcOjoIzquierdoVertical
     *
     * @param string $rcOjoIzquierdoVertical
     *
     * @return OrdenTrabajoContactologia
     */
    public function setRcOjoIzquierdoVertical($rcOjoIzquierdoVertical)
    {
        $this->rcOjoIzquierdoVertical = $rcOjoIzquierdoVertical;

        return $this;
    }

    /**
     * Get rcOjoIzquierdoVertical
     *
     * @return string
     */
    public function getRcOjoIzquierdoVertical()
    {
        return $this->rcOjoIzquierdoVertical;
    }

    /**
     * Set ojoDerechoCurvas
     *
     * @param string $ojoDerechoCurvas
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoDerechoCurvas($ojoDerechoCurvas)
    {
        $this->ojoDerechoCurvas = $ojoDerechoCurvas;

        return $this;
    }

    /**
     * Get ojoDerechoCurvas
     *
     * @return string
     */
    public function getOjoDerechoCurvas()
    {
        return $this->ojoDerechoCurvas;
    }

    /**
     * Set ojoIzquierdoCurvas
     *
     * @param string $ojoIzquierdoCurvas
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoIzquierdoCurvas($ojoIzquierdoCurvas)
    {
        $this->ojoIzquierdoCurvas = $ojoIzquierdoCurvas;

        return $this;
    }

    /**
     * Get ojoIzquierdoCurvas
     *
     * @return string
     */
    public function getOjoIzquierdoCurvas()
    {
        return $this->ojoIzquierdoCurvas;
    }

    /**
     * Set ojoDerechoDiametro
     *
     * @param string $ojoDerechoDiametro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoDerechoDiametro($ojoDerechoDiametro)
    {
        $this->ojoDerechoDiametro = $ojoDerechoDiametro;

        return $this;
    }

    /**
     * Get ojoDerechoDiametro
     *
     * @return string
     */
    public function getOjoDerechoDiametro()
    {
        return $this->ojoDerechoDiametro;
    }

    /**
     * Set ojoIzquierdoDiametro
     *
     * @param string $ojoIzquierdoDiametro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoIzquierdoDiametro($ojoIzquierdoDiametro)
    {
        $this->ojoIzquierdoDiametro = $ojoIzquierdoDiametro;

        return $this;
    }

    /**
     * Get ojoIzquierdoDiametro
     *
     * @return string
     */
    public function getOjoIzquierdoDiametro()
    {
        return $this->ojoIzquierdoDiametro;
    }

    /**
     * Set ojoDerechoAV
     *
     * @param string $ojoDerechoAV
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoDerechoAV($ojoDerechoAV)
    {
        $this->ojoDerechoAV = $ojoDerechoAV;

        return $this;
    }

    /**
     * Get ojoDerechoAV
     *
     * @return string
     */
    public function getOjoDerechoAV()
    {
        return $this->ojoDerechoAV;
    }

    /**
     * Set ojoIzquierdoAV
     *
     * @param string $ojoIzquierdoAV
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoIzquierdoAV($ojoIzquierdoAV)
    {
        $this->ojoIzquierdoAV = $ojoIzquierdoAV;

        return $this;
    }

    /**
     * Get ojoIzquierdoAV
     *
     * @return string
     */
    public function getOjoIzquierdoAV()
    {
        return $this->ojoIzquierdoAV;
    }

    /**
     * Set ojoDerechoCaracteristicas
     *
     * @param string $ojoDerechoCaracteristicas
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoDerechoCaracteristicas($ojoDerechoCaracteristicas)
    {
        $this->ojoDerechoCaracteristicas = $ojoDerechoCaracteristicas;

        return $this;
    }

    /**
     * Get ojoDerechoCaracteristicas
     *
     * @return string
     */
    public function getOjoDerechoCaracteristicas()
    {
        return $this->ojoDerechoCaracteristicas;
    }

    /**
     * Set ojoIzquierdoCaracteristicas
     *
     * @param string $ojoIzquierdoCaracteristicas
     *
     * @return OrdenTrabajoContactologia
     */
    public function setOjoIzquierdoCaracteristicas($ojoIzquierdoCaracteristicas)
    {
        $this->ojoIzquierdoCaracteristicas = $ojoIzquierdoCaracteristicas;

        return $this;
    }

    /**
     * Get ojoIzquierdoCaracteristicas
     *
     * @return string
     */
    public function getOjoIzquierdoCaracteristicas()
    {
        return $this->ojoIzquierdoCaracteristicas;
    }

    /**
     * Set lejosOjoDerechoEje
     *
     * @param string $lejosOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setLejosOjoDerechoEje($lejosOjoDerechoEje)
    {
        $this->lejosOjoDerechoEje = $lejosOjoDerechoEje;

        return $this;
    }

    /**
     * Get lejosOjoDerechoEje
     *
     * @return string
     */
    public function getLejosOjoDerechoEje()
    {
        return $this->lejosOjoDerechoEje;
    }

    /**
     * Set lejosOjoIzquierdoEje
     *
     * @param string $lejosOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setLejosOjoIzquierdoEje($lejosOjoIzquierdoEje)
    {
        $this->lejosOjoIzquierdoEje = $lejosOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoEje
     *
     * @return string
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @param integer $lejosOjoDerechoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setLejosOjoDerechoEsfera($lejosOjoDerechoEsfera)
    {
        $this->lejosOjoDerechoEsfera = $lejosOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get lejosOjoDerechoEsfera
     *
     * @return integer
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
     * @return OrdenTrabajoContactologia
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
     * @param string $cercaOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setCercaOjoDerechoEje($cercaOjoDerechoEje)
    {
        $this->cercaOjoDerechoEje = $cercaOjoDerechoEje;

        return $this;
    }

    /**
     * Get cercaOjoDerechoEje
     *
     * @return string
     */
    public function getCercaOjoDerechoEje()
    {
        return $this->cercaOjoDerechoEje;
    }

    /**
     * Set cercaOjoIzquierdoEje
     *
     * @param string $cercaOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setCercaOjoIzquierdoEje($cercaOjoIzquierdoEje)
    {
        $this->cercaOjoIzquierdoEje = $cercaOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoEje
     *
     * @return string
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set antesLejosOjoDerechoEje
     *
     * @param string $antesLejosOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setAntesLejosOjoDerechoEje($antesLejosOjoDerechoEje)
    {
        $this->antesLejosOjoDerechoEje = $antesLejosOjoDerechoEje;

        return $this;
    }

    /**
     * Get antesLejosOjoDerechoEje
     *
     * @return string
     */
    public function getAntesLejosOjoDerechoEje()
    {
        return $this->antesLejosOjoDerechoEje;
    }

    /**
     * Set antesLejosOjoIzquierdoEje
     *
     * @param string $antesLejosOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setAntesLejosOjoIzquierdoEje($antesLejosOjoIzquierdoEje)
    {
        $this->antesLejosOjoIzquierdoEje = $antesLejosOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get antesLejosOjoIzquierdoEje
     *
     * @return string
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @param integer $antesLejosOjoDerechoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setAntesLejosOjoDerechoEsfera($antesLejosOjoDerechoEsfera)
    {
        $this->antesLejosOjoDerechoEsfera = $antesLejosOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get antesLejosOjoDerechoEsfera
     *
     * @return integer
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
     * @return OrdenTrabajoContactologia
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
     * @param string $antesCercaOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setAntesCercaOjoDerechoEje($antesCercaOjoDerechoEje)
    {
        $this->antesCercaOjoDerechoEje = $antesCercaOjoDerechoEje;

        return $this;
    }

    /**
     * Get antesCercaOjoDerechoEje
     *
     * @return string
     */
    public function getAntesCercaOjoDerechoEje()
    {
        return $this->antesCercaOjoDerechoEje;
    }

    /**
     * Set antesCercaOjoIzquierdoEje
     *
     * @param string $antesCercaOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setAntesCercaOjoIzquierdoEje($antesCercaOjoIzquierdoEje)
    {
        $this->antesCercaOjoIzquierdoEje = $antesCercaOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get antesCercaOjoIzquierdoEje
     *
     * @return string
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set finalOjoIzquierdoDnp
     *
     * @param string $finalOjoIzquierdoDnp
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalOjoIzquierdoDnp($finalOjoIzquierdoDnp)
    {
        $this->finalOjoIzquierdoDnp = $finalOjoIzquierdoDnp;

        return $this;
    }

    /**
     * Get finalOjoIzquierdoDnp
     *
     * @return string
     */
    public function getFinalOjoIzquierdoDnp()
    {
        return $this->finalOjoIzquierdoDnp;
    }

    /*******************

    /**
     * Set finalOjoDerechoDnp
     *
     * @param string $finalOjoDerechoDnp
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalOjoDerechoDnp($finalOjoDerechoDnp)
    {
        $this->finalOjoDerechoDnp = $finalOjoDerechoDnp;

        return $this;
    }

    /**
     * Get finalOjoDerechoDnp
     *
     * @return string
     */
    public function getFinalOjoDerechoDnp()
    {
        return $this->finalOjoDerechoDnp;
    }


    /*******************

    /**
     * Set finalCercaOjoIzquierdoEsfera
     *
     * @param string $finalCercaOjoIzquierdoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoIzquierdoEsfera($finalCercaOjoIzquierdoEsfera)
    {
        $this->finalCercaOjoIzquierdoEsfera = $finalCercaOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get finalCercaOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getFinalCercaOjoIzquierdoEsfera()
    {
        return $this->finalCercaOjoIzquierdoEsfera;
    }


    /*******************

    /**
     * Set finalCercaOjoDerechoEsfera
     *
     * @param string $finalCercaOjoDerechoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoDerechoEsfera($finalCercaOjoDerechoEsfera)
    {
        $this->finalCercaOjoDerechoEsfera = $finalCercaOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get finalCercaOjoDerechoEsfera
     *
     * @return string
     */
    public function getFinalCercaOjoDerechoEsfera()
    {
        return $this->finalCercaOjoDerechoEsfera;
    }


    /*******************

    /**
     * Set finalCercaOjoIzquierdoCilindro
     *
     * @param string $finalCercaOjoIzquierdoCilindro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoIzquierdoCilindro($finalCercaOjoIzquierdoCilindro)
    {
        $this->finalCercaOjoIzquierdoCilindro = $finalCercaOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get finalCercaOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getFinalCercaOjoIzquierdoCilindro()
    {
        return $this->finalCercaOjoIzquierdoCilindro;
    }


    /*******************

    /**
     * Set finalCercaOjoDerechoCilindro
     *
     * @param string $finalCercaOjoDerechoCilindro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoDerechoCilindro($finalCercaOjoDerechoCilindro)
    {
        $this->finalCercaOjoDerechoCilindro = $finalCercaOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get finalCercaOjoDerechoCilindro
     *
     * @return string
     */
    public function getFinalCercaOjoDerechoCilindro()
    {
        return $this->finalCercaOjoDerechoCilindro;
    }


    /*******************

    /**
     * Set finalCercaOjoIzquierdoEje
     *
     * @param string $finalCercaOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoIzquierdoEje($finalCercaOjoIzquierdoEje)
    {
        $this->finalCercaOjoIzquierdoEje = $finalCercaOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get finalCercaOjoIzquierdoEje
     *
     * @return string
     */
    public function getFinalCercaOjoIzquierdoEje()
    {
        return $this->finalCercaOjoIzquierdoEje;
    }


    /*******************

    /**
     * Set finalCercaOjoDerechoEje
     *
     * @param string $finalCercaOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalCercaOjoDerechoEje($finalCercaOjoDerechoEje)
    {
        $this->finalCercaOjoDerechoEje = $finalCercaOjoDerechoEje;

        return $this;
    }

    /**
     * Get finalCercaOjoDerechoEje
     *
     * @return string
     */
    public function getFinalCercaOjoDerechoEje()
    {
        return $this->finalCercaOjoDerechoEje;
    }


    /*******************

    /**
     * Set finalLejosOjoIzquierdoEsfera
     *
     * @param string $finalLejosOjoIzquierdoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoIzquierdoEsfera($finalLejosOjoIzquierdoEsfera)
    {
        $this->finalLejosOjoIzquierdoEsfera = $finalLejosOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get finalLejosOjoIzquierdoEsfera
     *
     * @return string
     */
    public function getFinalLejosOjoIzquierdoEsfera()
    {
        return $this->finalLejosOjoIzquierdoEsfera;
    }


    /*******************

    /**
     * Set finalLejosOjoDerechoEsfera
     *
     * @param string $finalLejosOjoDerechoEsfera
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoDerechoEsfera($finalLejosOjoDerechoEsfera)
    {
        $this->finalLejosOjoDerechoEsfera = $finalLejosOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get finalLejosOjoDerechoEsfera
     *
     * @return string
     */
    public function getFinalLejosOjoDerechoEsfera()
    {
        return $this->finalLejosOjoDerechoEsfera;
    }


    /*******************

    /**
     * Set finalLejosOjoIzquierdoCilindro
     *
     * @param string $finalLejosOjoIzquierdoCilindro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoIzquierdoCilindro($finalLejosOjoIzquierdoCilindro)
    {
        $this->finalLejosOjoIzquierdoCilindro = $finalLejosOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get finalLejosOjoIzquierdoCilindro
     *
     * @return string
     */
    public function getFinalLejosOjoIzquierdoCilindro()
    {
        return $this->finalLejosOjoIzquierdoCilindro;
    }


    /*******************

    /**
     * Set finalLejosOjoDerechoCilindro
     *
     * @param string $finalLejosOjoDerechoCilindro
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoDerechoCilindro($finalLejosOjoDerechoCilindro)
    {
        $this->finalLejosOjoDerechoCilindro = $finalLejosOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get finalLejosOjoDerechoCilindro
     *
     * @return string
     */
    public function getFinalLejosOjoDerechoCilindro()
    {
        return $this->finalLejosOjoDerechoCilindro;
    }


    /*******************

    /**
     * Set finalLejosOjoIzquierdoEje
     *
     * @param string $finalLejosOjoIzquierdoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoIzquierdoEje($finalLejosOjoIzquierdoEje)
    {
        $this->finalLejosOjoIzquierdoEje = $finalLejosOjoIzquierdoEje;

        return $this;
    }

    /**
     * Get finalLejosOjoIzquierdoEje
     *
     * @return string
     */
    public function getFinalLejosOjoIzquierdoEje()
    {
        return $this->finalLejosOjoIzquierdoEje;
    }


    /*******************

    /**
     * Set finalLejosOjoDerechoEje
     *
     * @param string $finalLejosOjoDerechoEje
     *
     * @return OrdenTrabajoContactologia
     */
    public function setFinalLejosOjoDerechoEje($finalLejosOjoDerechoEje)
    {
        $this->finalLejosOjoDerechoEje = $finalLejosOjoDerechoEje;

        return $this;
    }

    /**
     * Get finalLejosOjoDerechoEje
     *
     * @return string
     */
    public function getFinalLejosOjoDerechoEje()
    {
        return $this->finalLejosOjoDerechoEje;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set comprobante
     *
     * @param \AppBundle\Entity\Comprobante $comprobante
     *
     * @return OrdenTrabajoContactologia
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
     * Set medico
     *
     * @param \AppBundle\Entity\Medico $medico
     *
     * @return OrdenTrabajoContactologia
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set obraSocialPlan
     *
     * @param \AppBundle\Entity\ObraSocialPlan $obraSocialPlan
     *
     * @return OrdenTrabajoContactologia
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
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return OrdenTrabajoContactologia
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
    /*************************TALLER*******/
    /**
     * Set taller
     *
     * @param \AppBundle\Entity\Taller $taller
     *
     * @return OrdenTrabajoContactologia
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
     * Set fechaTallerPedido
     *
     * @param \DateTime $fechaTallerPedido
     *
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * @return OrdenTrabajoContactologia
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
     * Set numeroTaller
     *
     * @param integer $numeroTaller
     *
     * @return OrdenTrabajoContactologia
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

//    /**
//     * Set otrosTrabajos
//     *
//     * @param string $otrosTrabajos
//     *
//     * @return OrdenTrabajoContactologia
//     */
//    public function setOtrosTrabajos($otrosTrabajos)
//    {
//        $this->otrosTrabajos = $otrosTrabajos;
//
//        return $this;
//    }
//
//    /**
//     * Get otrosTrabajos
//     *
//     * @return string
//     */
//    public function getOtrosTrabajos()
//    {
//        return $this->otrosTrabajos;
//    }
    /*************************TALLER*******/
}
