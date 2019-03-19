<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenTrabajoDetalle
 *
 * @ORM\Table(name="orden_trabajo_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenTrabajoDetalleRepository")
 */
class OrdenTrabajoDetalle
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
     * @var \ordenTrabajo
     *
     * @ORM\ManyToOne(targetEntity="OrdenTrabajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orden_trabajo_id", referencedColumnName="id")
     * })
     */
    private $ordenTrabajo;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_derecho_eje", type="integer")
     */
    private $lejosOjoDerechoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_eje", type="integer")
     */
    private $lejosOjoIzquierdoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_derecho_cilindro", type="integer")
     */
    private $lejosOjoDerechoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_cilindro", type="integer")
     */
    private $lejosOjoIzquierdoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_derecho_esfera", type="integer")
     */
    private $lejosOjoDerechoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="lejos_ojo_izquierdo_esfera", type="integer")
     */
    private $lejosOjoIzquierdoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_derecho_eje", type="integer")
     */
    private $cercaOjoDerechoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_eje", type="integer")
     */
    private $cercaOjoIzquierdoEje;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_derecho_cilindro", type="integer")
     */
    private $cercaOjoDerechoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_cilindro", type="integer")
     */
    private $cercaOjoIzquierdoCilindro;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_derecho_esfera", type="integer")
     */
    private $cercaOjoDerechoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="cerca_ojo_izquierdo_esfera", type="integer")
     */
    private $cercaOjoIzquierdoEsfera;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_derecho_dnp", type="integer")
     */
    private $ojoDerechoDnp;

    /**
     * @var int
     *
     * @ORM\Column(name="ojo_izquierdo_dnp", type="integer")
     */
    private $ojoIzquierdoDnp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime", nullable=true)
     */
    private $fechaEntrega;

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
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;


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
     * Set lejosOjoDerechoEje
     *
     * @param integer $lejosOjoDerechoEje
     *
     * @return OrdenTrabajoDetalle
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
     * @param integer $lejosOjoIzquieroEje
     *
     * @return OrdenTrabajoDetalle
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
     * @param integer $lejosOjoDerechoCilindro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setLejosOjoDerechoCilindro($lejosOjoDerechoCilindro)
    {
        $this->lejosOjoDerechoCilindro = $lejosOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get lejosOjoDerechoCilindro
     *
     * @return integer
     */
    public function getLejosOjoDerechoCilindro()
    {
        return $this->lejosOjoDerechoCilindro;
    }

    /**
     * Set lejosOjoIzquierdoCilindro
     *
     * @param integer $lejosOjoIzquierdoCilindro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setLejosOjoIzquierdoCilindro($lejosOjoIzquierdoCilindro)
    {
        $this->lejosOjoIzquierdoCilindro = $lejosOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoCilindro
     *
     * @return integer
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
     * @return OrdenTrabajoDetalle
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
     * @param integer $lejosOjoIzquierdoEsfera
     *
     * @return OrdenTrabajoDetalle
     */
    public function setLejosOjoIzquierdoEsfera($lejosOjoIzquierdoEsfera)
    {
        $this->lejosOjoIzquierdoEsfera = $lejosOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get lejosOjoIzquierdoEsfera
     *
     * @return integer
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
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
     * @param integer $cercaOjoDerechoCilindro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setCercaOjoDerechoCilindro($cercaOjoDerechoCilindro)
    {
        $this->cercaOjoDerechoCilindro = $cercaOjoDerechoCilindro;

        return $this;
    }

    /**
     * Get cercaOjoDerechoCilindro
     *
     * @return integer
     */
    public function getCercaOjoDerechoCilindro()
    {
        return $this->cercaOjoDerechoCilindro;
    }

    /**
     * Set cercaOjoIzquierdoCilindro
     *
     * @param integer $cercaOjoIzquierdoCilindro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setCercaOjoIzquierdoCilindro($cercaOjoIzquierdoCilindro)
    {
        $this->cercaOjoIzquierdoCilindro = $cercaOjoIzquierdoCilindro;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoCilindro
     *
     * @return integer
     */
    public function getCercaOjoIzquierdoCilindro()
    {
        return $this->cercaOjoIzquierdoCilindro;
    }

    /**
     * Set cercaOjoDerechoEsfera
     *
     * @param integer $cercaOjoDerechoEsfera
     *
     * @return OrdenTrabajoDetalle
     */
    public function setCercaOjoDerechoEsfera($cercaOjoDerechoEsfera)
    {
        $this->cercaOjoDerechoEsfera = $cercaOjoDerechoEsfera;

        return $this;
    }

    /**
     * Get cercaOjoDerechoEsfera
     *
     * @return integer
     */
    public function getCercaOjoDerechoEsfera()
    {
        return $this->cercaOjoDerechoEsfera;
    }

    /**
     * Set cercaOjoIzquierdoEsfera
     *
     * @param integer $cercaOjoIzquierdoEsfera
     *
     * @return OrdenTrabajoDetalle
     */
    public function setCercaOjoIzquierdoEsfera($cercaOjoIzquierdoEsfera)
    {
        $this->cercaOjoIzquierdoEsfera = $cercaOjoIzquierdoEsfera;

        return $this;
    }

    /**
     * Get cercaOjoIzquierdoEsfera
     *
     * @return integer
     */
    public function getCercaOjoIzquierdoEsfera()
    {
        return $this->cercaOjoIzquierdoEsfera;
    }

    /**
     * Set ojoDerechoDnp
     *
     * @param integer $ojoDerechoDnp
     *
     * @return OrdenTrabajoDetalle
     */
    public function setOjoDerechoDnp($ojoDerechoDnp)
    {
        $this->ojoDerechoDnp = $ojoDerechoDnp;

        return $this;
    }

    /**
     * Get ojoDerechoDnp
     *
     * @return integer
     */
    public function getOjoDerechoDnp()
    {
        return $this->ojoDerechoDnp;
    }

    /**
     * Set ojoIzquierdoDnp
     *
     * @param integer $ojoIzquierdoDnp
     *
     * @return OrdenTrabajoDetalle
     */
    public function setOjoIzquierdoDnp($ojoIzquierdoDnp)
    {
        $this->ojoIzquierdoDnp = $ojoIzquierdoDnp;

        return $this;
    }

    /**
     * Get ojoIzquierdoDnp
     *
     * @return integer
     */
    public function getOjoIzquierdoDnp()
    {
        return $this->ojoIzquierdoDnp;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     *
     * @return OrdenTrabajoDetalle
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
     * Set estado
     *
     * @param string $estado
     *
     * @return OrdenTrabajoDetalle
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
     * Set ordenTrabajo
     *
     * @param \AppBundle\Entity\OrdenTrabajo $ordenTrabajo
     *
     * @return OrdenTrabajoDetalle
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
}
