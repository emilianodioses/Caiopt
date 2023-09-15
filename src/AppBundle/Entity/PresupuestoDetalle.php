<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PresupuestoDetalle
 *
 * @ORM\Table(name="presupuesto_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PresupuestoDetalleRepository")
 */
class PresupuestoDetalle
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
     * @ORM\ManyToOne(targetEntity="Presupuesto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPresupuesto", referencedColumnName="id")
     * })
     */
    private $presupuesto;
    /**
     * @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idArticulo", referencedColumnName="id")
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
     * @ORM\Column(name="precio_unit", type="decimal", precision=16, scale=2)
     */
    private $precioUnit = '0';
    /**
     * @var string
     *
     * @ORM\Column(name="importe_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $importeBonificacion = '0';
    /**
     * @var string
     *
     * @ORM\Column(name="totalDetalle", type="decimal", precision=16, scale=2)
     */
    private $totalDetalle = '0';
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
     * @var string
     *
     * @ORM\Column(name="porcentaje_bonificacion", type="decimal", precision=16, scale=2, nullable=true)
     */
    private $porcentajeBonificacion;

    
    /**
     * @var \Parametro
     *
     * @ORM\ManyToOne(targetEntity="Parametro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_parametro", referencedColumnName="id")
     * })
     */
    private $parametro;

    /**
     * @var int
     *
     * @ORM\Column(name="valorNro", type="integer")
     */
    private $valorNro;


    /****************************************************************
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set presupuesto
     *
     * @param \AppBundle\Entity\Presupuesto $presupuesto
     *
     * @return PresupuestoDetalle
     */
    public function setPresupuesto(\AppBundle\Entity\Presupuesto $presupuesto  = null)
    {
        $this->presupuesto = $presupuesto;

        return $this;
    }
    /**
     * Get presupuesto
     *
     * @return \AppBundle\Entity\Presupuesto
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }
    /**
     * Set articulo
     *
     * @param \AppBundle\Entity\Articulo $articulo
     *
     * @return PresupuestoDetalle
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
     * Set cantidad
     *
     * @param integer $cantidad
     *
     * @return PresupuestoDetalle
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
     * Set precioUnit
     *
     * @param string $precioUnit
     *
     * @return PresupuestoDetalle
     */
    public function setPrecioUnit($precioUnit)
    {
        $this->precioUnit = $precioUnit;

        return $this;
    }
    /**
     * Get precioUnit
     *
     * @return string
     */
    public function getPrecioUnit()
    {
        return $this->precioUnit;
    }
    /**
     * Set importeBonificacion
     *
     * @param string $importeBonificacion
     *
     * @return PresupuestoDetalle
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
     * Set totalDetalle
     *
     * @param string $totalDetalle
     *
     * @return PresupuestoDetalle
     */
    public function setTotalDetalle($totalDetalle)
    {
        $this->totalDetalle = $totalDetalle;

        return $this;
    }
    /**
     * Get totalDetalle
     *
     * @return string
     */
    public function getTotalDetalle()
    {
        return $this->totalDetalle;
    }
    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return PresupuestoDetalle
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
     * @return PresupuestoDetalle
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
     * @return PresupuestoDetalle
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
     * @return PresupuestoDetalle
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
     * @return PresupuestoDetalle
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
     * Set parametro
     *
     * @param \AppBundle\Entity\Parametro $parametro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setParametro(\AppBundle\Entity\Parametro $parametro = null)
    {
        $this->parametro = $parametro;

        return $this;
    }

    /**
     * Get parametro
     *
     * @return \AppBundle\Entity\Parametro
     */
    public function getParametro()
    {
        return $this->parametro;
    }

    /**
     * Set valorNro
     *
     * @param integer $valorNro
     *
     * @return OrdenTrabajoDetalle
     */
    public function setValorNro($valorNro)
    {
        $this->valorNro = $valorNro;

        return $this;
    }

    /**
     * Get valorNro
     *
     * @return integer
     */
    public function getValorNro()
    {
        return $this->valorNro;
    }
    /**
     * Set porcentajeBonificacion
     *
     * @param string $porcentajeBonificacion
     *
     * @return OrdenTrabajoDetalle
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
}