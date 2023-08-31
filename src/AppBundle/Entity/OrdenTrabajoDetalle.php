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
     * @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="articulo_id", referencedColumnName="id")
     * })
     */
    private $articulo;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $importeBonificacion;

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
     *   @ORM\JoinColumn(name="parametro_id", referencedColumnName="id")
     * })
     */
    private $parametro;

    /**
     * @var string
     *
     * @ORM\Column(name="valorNro", type="decimal", precision=16, scale=2)
     */
    private $valorNro;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    private $total; 

    /**
     * @var string
     *
     * @ORM\Column(name="precio_venta", type="decimal", precision=16, scale=2)
     */
    private $precioVenta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_entrega", type="datetime", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_cristal", type="string", length=255)
     */
    private $tipoCristal;

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
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

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

    /**
     * Set articulo
     *
     * @param \AppBundle\Entity\Articulo $articulo
     *
     * @return OrdenTrabajoDetalle
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
     * Set importeBonificacion
     *
     * @param string $importeBonificacion
     *
     * @return OrdenTrabajoDetalle
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
     * Set total
     *
     * @param string $total
     *
     * @return OrdenTrabajoDetalle
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
     * Set precioVenta
     *
     * @param string $precioVenta
     *
     * @return OrdenTrabajoDetalle
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
     * Set tipoCristal
     *
     * @param string $tipoCristal
     *
     * @return OrdenTrabajoDetalle
     */
    public function setTipoCristal($tipoCristal)
    {
        $this->tipoCristal = $tipoCristal;

        return $this;
    }

    /**
     * Get tipoCristal
     *
     * @return string
     */
    public function getTipoCristal()
    {
        return $this->tipoCristal;
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

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return OrdenTrabajoDetalle
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
     * @return OrdenTrabajoDetalle
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
