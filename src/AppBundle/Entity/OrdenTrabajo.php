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
     * @ORM\Column(name="referencia", type="string", length=255, nullable=true)
     */
    private $referencia;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="cristales", type="string", length=255, nullable=true)
     */
    private $cristales;

    /**
     * @var string
     *
     * @ORM\Column(name="montura", type="string", length=255, nullable=true)
     */
    private $montura;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="otros_trabajos", type="string", length=255, nullable=true)
     */
    private $otrosTrabajos;

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
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_estimados", type="integer", nullable=true)
     */
    private $diasEstimados;

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
     * @var ArrayCollection OrdenTrabajoDetalle
     */
    protected $ordenTrabajoDetalles;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;


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
     * Set referencia
     *
     * @param string $referencia
     *
     * @return OrdenTrabajo
     */
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;

        return $this;
    }

    /**
     * Get referencia
     *
     * @return string
     */
    public function getReferencia()
    {
        return $this->referencia;
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
     * Set cristales
     *
     * @param string $cristales
     *
     * @return OrdenTrabajo
     */
    public function setCristales($cristales)
    {
        $this->cristales = $cristales;

        return $this;
    }

    /**
     * Get cristales
     *
     * @return string
     */
    public function getCristales()
    {
        return $this->cristales;
    }

    /**
     * Set montura
     *
     * @param string $montura
     *
     * @return OrdenTrabajo
     */
    public function setMontura($montura)
    {
        $this->montura = $montura;

        return $this;
    }

    /**
     * Get montura
     *
     * @return string
     */
    public function getMontura()
    {
        return $this->montura;
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return OrdenTrabajo
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
     * Set diasEstimados
     *
     * @param integer $diasEstimados
     *
     * @return OrdenTrabajo
     */
    public function setDiasEstimados($diasEstimados)
    {
        $this->diasEstimados = $diasEstimados;

        return $this;
    }

    /**
     * Get diasEstimados
     *
     * @return integer
     */
    public function getDiasEstimados()
    {
        return $this->diasEstimados;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return OrdenTrabajo
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
     * Set updatedBy
     *
     * @param integer $updatedBy
     *
     * @return OrdenTrabajo
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
}
