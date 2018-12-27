<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_eje", type="integer")
     */
    private $ojoDerechoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_cilindro", type="integer")
     */
    private $ojoDerechoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_esfera", type="integer")
     */
    private $ojoDerechoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_adicc", type="integer")
     */
    private $ojoDerechoAdicc;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_dnp", type="integer")
     */
    private $ojoDerechoDnp;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_alt", type="integer")
     */
    private $ojoDerechoAlt;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_eje", type="integer")
     */
    private $ojoIzquierdoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_cilindro", type="integer")
     */
    private $ojoIzquierdoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_esfera", type="integer")
     */
    private $ojoIzquierdoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_adicc", type="integer")
     */
    private $ojoIzquierdoAdicc;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_dnp", type="integer")
     */
    private $ojoIzquierdoDnp;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_alt", type="integer")
     */
    private $ojoIzquierdoAlt;

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
     * @var int
     *
     * @ORM\Column(name="dip", type="integer")
     */
    private $dip;

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
     *   @ORM\JoinColumn(name="id", referencedColumnName="id", nullable=true)
     * })
     */
    private $taller;

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
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;


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
     * Set cliente
     *
     * @param \stdClass $cliente
     *
     * @return OrdentrAbajo
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \stdClass
     */
    public function getCliente()
    {
        return $this->cliente;
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
     * Set ojoDerechoEje
     *
     * @param integer $ojoDerechoEje
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoEje($ojoDerechoEje)
    {
        $this->ojoDerechoEje = $ojoDerechoEje;

        return $this;
    }

    /**
     * Get ojoDerechoEje
     *
     * @return int
     */
    public function getOjoDerechoEje()
    {
        return $this->ojoDerechoEje;
    }

    /**
     * Set ojoDerechoCilindro
     *
     * @param integer $ojoDerechoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoCilindro($ojoDerechoCilindro)
    {
        $this->ojoDerechoCilindro = $ojoDerechoCilindro;

        return $this;
    }

    /**
     * Get ojoDerechoCilindro
     *
     * @return int
     */
    public function getOjoDerechoCilindro()
    {
        return $this->ojoDerechoCilindro;
    }

    /**
     * Set ojoDerechoEsfera
     *
     * @param integer $ojoDerechoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoEsfera($ojoDerechoEsfera)
    {
        $this->ojoDerechoEsfera = $ojoDerechoEsfera;

        return $this;
    }

    /**
     * Get ojoDerechoEsfera
     *
     * @return int
     */
    public function getOjoDerechoEsfera()
    {
        return $this->ojoDerechoEsfera;
    }

    /**
     * Set ojoDerechoAdicc
     *
     * @param integer $ojoDerechoAdicc
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoAdicc($ojoDerechoAdicc)
    {
        $this->ojoDerechoAdicc = $ojoDerechoAdicc;

        return $this;
    }

    /**
     * Get ojoDerechoAdicc
     *
     * @return int
     */
    public function getOjoDerechoAdicc()
    {
        return $this->ojoDerechoAdicc;
    }

    /**
     * Set ojoDerechoDnp
     *
     * @param integer $ojoDerechoDnp
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
     * @return int
     */
    public function getOjoDerechoDnp()
    {
        return $this->ojoDerechoDnp;
    }

    /**
     * Set ojoDerechoAlt
     *
     * @param integer $ojoDerechoAlt
     *
     * @return OrdenTrabajo
     */
    public function setOjoDerechoAlt($ojoDerechoAlt)
    {
        $this->ojoDerechoAlt = $ojoDerechoAlt;

        return $this;
    }

    /**
     * Get ojoDerechoAlt
     *
     * @return int
     */
    public function getOjoDerechoAlt()
    {
        return $this->ojoDerechoAlt;
    }

    /**
     * Set ojoIzquierdoEje
     *
     * @param integer $ojoIzquierdoEje
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoEje($ojoIzquierdoEje)
    {
        $this->ojoIzquierdoEje = $ojoIzquierdoEje;

        return $this;
    }

    /**
     * Get ojoIzquierdoEje
     *
     * @return int
     */
    public function getOjoIzquierdoEje()
    {
        return $this->ojoIzquierdoEje;
    }

    /**
     * Set ojoIzquierdoCilindro
     *
     * @param integer $ojoIzquierdoCilindro
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoCilindro($ojoIzquierdoCilindro)
    {
        $this->ojoIzquierdoCilindro = $ojoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get ojoIzquierdoCilindro
     *
     * @return int
     */
    public function getOjoIzquierdoCilindro()
    {
        return $this->ojoIzquierdoCilindro;
    }

    /**
     * Set ojoIzquierdoEsfera
     *
     * @param integer $ojoIzquierdoEsfera
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoEsfera($ojoIzquierdoEsfera)
    {
        $this->ojoIzquierdoEsfera = $ojoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get ojoIzquierdoEsfera
     *
     * @return int
     */
    public function getOjoIzquierdoEsfera()
    {
        return $this->ojoIzquierdoEsfera;
    }

    /**
     * Set ojoIzquierdoAdicc
     *
     * @param integer $ojoIzquierdoAdicc
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoAdicc($ojoIzquierdoAdicc)
    {
        $this->ojoIzquierdoAdicc = $ojoIzquierdoAdicc;

        return $this;
    }

    /**
     * Get ojoIzquierdoAdicc
     *
     * @return int
     */
    public function getOjoIzquierdoAdicc()
    {
        return $this->ojoIzquierdoAdicc;
    }

    /**
     * Set ojoIzquierdoDnp
     *
     * @param integer $ojoIzquierdoDnp
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
     * @return int
     */
    public function getOjoIzquierdoDnp()
    {
        return $this->ojoIzquierdoDnp;
    }

    /**
     * Set ojoIzquierdoAlt
     *
     * @param integer $ojoIzquierdoAlt
     *
     * @return OrdenTrabajo
     */
    public function setOjoIzquierdoAlt($ojoIzquierdoAlt)
    {
        $this->ojoIzquierdoAlt = $ojoIzquierdoAlt;

        return $this;
    }

    /**
     * Get ojoIzquierdoAlt
     *
     * @return int
     */
    public function getOjoIzquierdoAlt()
    {
        return $this->ojoIzquierdoAlt;
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
     * Set dip
     *
     * @param integer $dip
     *
     * @return OrdenTrabajo
     */
    public function setDip($dip)
    {
        $this->dip = $dip;

        return $this;
    }

    /**
     * Get dip
     *
     * @return int
     */
    public function getDip()
    {
        return $this->dip;
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
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set taller
     *
     * @param integer $taller
     *
     * @return OrdenTrabajo
     */
    public function setTaller($taller)
    {
        $this->taller = $taller;

        return $this;
    }

    /**
     * Get taller
     *
     * @return int
     */
    public function getTaller()
    {
        return $this->taller;
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
     * @return int
     */
    public function getDiasEstimados()
    {
        return $this->diasEstimados;
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
}
