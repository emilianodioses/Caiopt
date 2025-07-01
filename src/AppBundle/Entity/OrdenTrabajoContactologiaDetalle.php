<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenTrabajoContactologiaDetalle
 *
 * @ORM\Table(name="orden_trabajo_contactologia_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdenTrabajoContactologiaDetalleRepository")
 */
class OrdenTrabajoContactologiaDetalle
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
     * @ORM\ManyToOne(targetEntity="OrdenTrabajoContactologia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orden_trabajo_contactologia_id", referencedColumnName="id")
     * })
     */
    private $ordenTrabajoContactologia;

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
     * @ORM\Column(name="porcentaje_bonificacion", type="decimal", precision=16, scale=2)
     */
    private $porcentajeBonificacion;

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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * Set ordenTrabajoContactologia
     *
     * @param \AppBundle\Entity\OrdenTrabajoContactologia $ordenTrabajoContactologia
     *
     * @return OrdenTrabajoContactologiaDetalle
     */
    public function setOrdenTrabajo(\AppBundle\Entity\OrdenTrabajoContactologia $ordenTrabajoContactologia = null)
    {
        $this->ordenTrabajoContactologia = $ordenTrabajoContactologia;

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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * Set ordenTrabajoContactologia
     *
     * @param \AppBundle\Entity\OrdenTrabajoContactologia $ordenTrabajoContactologia
     *
     * @return OrdenTrabajoContactologiaDetalle
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
     * Set tipoCristal
     *
     * @param string $tipoCristal
     *
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
     * @var \Parametro
     *
     * @ORM\ManyToOne(targetEntity="Parametro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parametro_id", referencedColumnName="id")
     * })
     */
    private $parametro;

    /**
     * @var int
     *
     * @ORM\Column(name="valorNro", type="integer")
     */
    private $valorNro;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return OrdenTrabajoContactologiaDetalle
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
     * @return OrdenTrabajoContactologiaDetalle
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
