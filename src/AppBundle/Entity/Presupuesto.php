<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Repository\PresupuestoRepository;

/**
 * Presupuesto
 *
 * @ORM\Table(name="presupuesto")
 * @ORM\Entity(repositoryClass=PresupuestoRepository::class)
 */
class Presupuesto
{
    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCliente", referencedColumnName="id")
     * })
     */
    private $cliente;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaPresup", type="datetime", nullable=true)
     */
    private $fechaPresup;
    /**
     * @var int
     *
     * @ORM\Column(name="plazoEntrega", type="integer")
     */
    private $plazoEntrega;
    /**
     * @var int
     *
     * @ORM\Column(name="validez_presupuesto", type="integer")
     */
    private $validezPresupuesto;
    /**
     * @var string
     *
     * @ORM\Column(name="retiro", type="string", length=255, unique=true)
     */
    private $retiro;
    /**
     * @var int
     *
     * @ORM\Column(name="idCliente", type="integer")
     */
    private $idCliente;
    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    private $total = '0';
    /**
     * @var string
     *
     * @ORM\Column(name="iva_21", type="decimal", precision=16, scale=2)
     */
    private $iva21 = '0';
    /**
     * @var string
     *
     * @ORM\Column(name="total_presupuesto", type="decimal", precision=16, scale=2)
     */
    private $totalPresupuesto = '0';
    /**
     * @var string
     *
     * @ORM\Column(name="moneda", type="string", length=255, unique=true)
     */
    private $moneda;
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
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;
    /**
     * @var ArrayCollection PresupuestoDetalle
     */
    protected $presupuestoDetalles;

    /**
     * @var string
     *
     * @ORM\Column(name="total_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $totalBonificacion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**************************************************************
    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return Presupuesto
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set fechaPresup
     *
     * @param \DateTime $fechaPresup
     *
     * @return Presupuesto
     */
    public function setFechaPresup($fechaPresup)
    {
        $this->fechaPresup = $fechaPresup;

        return $this;
    }
    /**
     * Get fechaPresup
     *
     * @return \DateTime
     */
    public function getFechaPresup()
    {
        return $this->fechaPresup;
    }
    /**
     * Set plazoEntrega
     *
     * @param integer $plazoEntrega
     *
     * @return Presupuesto
     */
    public function setPlazoEntrega($plazoEntrega)
    {
        $this->plazoEntrega = $plazoEntrega;

        return $this;
    }
    /**
     * Get plazoEntrega
     *
     * @return int
     */
    public function getPlazoEntrega()
    {
        return $this->plazoEntrega;
    }
     /**
     * Set validezPresupuesto
     *
     * @param integer $validezPresupuesto
     *
     * @return Presupuesto
     */
    public function setValidezPresupuesto($validezPresupuesto)
    {
        $this->validezPresupuesto = $validezPresupuesto;

        return $this;
    }
    /**
     * Get plazoEntrega
     *
     * @return int
     */
    public function getValidezPresupuesto()
    {
        return $this->validezPresupuesto;
    }
    /**
     * Set retiro
     *
     * @param string $retiro
     *
     * @return Presupuesto
     */
    public function setRetiro($retiro)
    {
        $this->retiro = $retiro;

        return $this;
    }
    /**
     * Get retiro
     *
     * @return string
     */
    public function getRetiro()
    {
        return $this->retiro;
    }
    /**
     * Set idCliente
     *
     * @param integer $idCliente
     *
     * @return Presupuesto
     */
    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;

        return $this;
    }
    /**
     * Get idCliente
     *
     * @return integer
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }
    /**
     * Set total
     *
     * @param string $total
     *
     * @return Presupuesto
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
     * Set iva21
     *
     * @param string $iva21
     *
     * @return Presupuesto
     */
    public function setIva21($iva21)
    {
        $this->iva21 = $iva21;

        return $this;
    }
    /**
     * Get iva21
     *
     * @return string
     */
    public function getIva21()
    {
        return $this->iva21;
    }
    /**
     * Set totalPresupuesto
     *
     * @param string $totalPresupuesto
     *
     * @return Presupuesto
     */
    public function setTotalPresupuesto($totalPresupuesto)
    {
        $this->totalPresupuesto = $totalPresupuesto;

        return $this;
    }
    /**
     * Get totalPresupuesto
     *
     * @return string
     */
    public function getTotalPresupuesto()
    {
        return $this->totalPresupuesto;
    }
    /**
     * Set moneda
     *
     * @param string $moneda
     *
     * @return Presupuesto
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }
    /**
     * Get moneda
     *
     * @return string
     */
    public function getMoneda()
    {
        return $this->moneda;
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
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Presupuesto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }
    /**
     * Get activo
     *
     * @return bool
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
     * @return Presupuesto
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
     * @return Presupuesto
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return Presupuesto
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
     * @param \AppBundle\Entity\v $updatedBy
     *
     * @return Presupuesto
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
    public function __construct()
    {
        $this->presupuestoDetalles = new ArrayCollection();
    }

    public function getPresupuestoDetalles()
    {
        if (is_null($this->presupuestoDetalles)) {
            $this->presupuestoDetalles = new ArrayCollection();
        }
        return $this->presupuestoDetalles;
    }

    public function addPresupuestoDetalle(PresupuestoDetalle $presupuestoDetalle)
    {
        $this->presupuestoDetalles->add($presupuestoDetalle);
    }

    public function removePresupuestoDetalle(PresupuestoDetalle $presupuestoDetalle)
    {
        $this->presupuestoDetalles->remove($presupuestoDetalle);
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


}