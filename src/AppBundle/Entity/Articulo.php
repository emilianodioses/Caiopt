<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Articulo
 *
 * @ORM\Table(name="articulo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticuloRepository")
 */
class Articulo
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
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @var \ArticuloCategoria
     *
     * @ORM\ManyToOne(targetEntity="ArticuloCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * })
     */
    private $categoria;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_costo", type="decimal", precision=16, scale=2)
     */
    private $precioCosto;

    /**
     * @var string
     *
     * @ORM\Column(name="ganancia_porcentaje", type="decimal", precision=16, scale=2)
     */
    private $gananciaPorcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_venta", type="decimal", precision=16, scale=2)
     */
    private $precioVenta;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="precio_venta_sin_iva", type="decimal", precision=16, scale=2)
//     */
//    private $precioVentaSinIva;

    /**
     * @var \ArticuloMarca
     *
     * @ORM\ManyToOne(targetEntity="ArticuloMarca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca_id", referencedColumnName="id")
     * })
     */
    private $marca;

    /**
     * @var \AfipAlicuota
     *
     * @ORM\ManyToOne(targetEntity="AfipAlicuota")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="afip_alicuota_id", referencedColumnName="id")
     * })
     */
    private $iva;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_modifica", type="boolean")
     */
    private $precioModifica;

    /**
     * @var string
     *
     * @ORM\Column(name="orden_trabajo", type="boolean")
     */
    private $ordenTrabajo;
        /**
     * @var string
     *
     * @ORM\Column(name="forma", type="string", length=255, nullable=true)
     */
    private $forma;

    /**
     * @var string
     *
     * @ORM\Column(name="color_marco", type="string", length=255, nullable=true)
     */
    private $colorMarco;

    /**
     * @var \ArticuloMarco
     *
     * @ORM\ManyToOne(targetEntity="ArticuloMarco")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMarco", referencedColumnName="id")
     * })
     */
    private $marco;
    /**
     * @var string
     *
     * @ORM\Column(name="color_cristal", type="string", length=255, nullable=true)
     */
    private $colorCristal;

    /**
     * @var string
     *
     * @ORM\Column(name="alto", type="decimal", precision=16, scale=2, nullable=true)
     */
    private $alto;
    /**
     * @var string
     *
     * @ORM\Column(name="mayor_distancia", type="decimal", precision=16, scale=2, nullable=true)
     */
    private $mayor_distancia;
    /**
     * @var string
     *
     * @ORM\Column(name="puente", type="decimal", precision=16, scale=2, nullable=true)
     */
    private $puente;
    /**
     * @var string
     *
     * @ORM\Column(name="ancho", type="decimal", precision=16, scale=2, nullable=true)
     */
    private $ancho;
    /**
     * @var \ArticuloEstilo
     *
     * @ORM\ManyToOne(targetEntity="ArticuloEstilo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTipoAro", referencedColumnName="id", nullable=true)
     * })
     */
    private $tipoAro;
     /**
     * @var \Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ultimo_comprobante_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $ultimoComprobante;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Articulo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set categoria
     *
     * @param string $categoria
     *
     * @return Articulo
     */
    public function setcategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return string
     */
    public function getcategoria()
    {
        return $this->categoria;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Articulo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set precioCosto
     *
     * @param string $precioCosto
     *
     * @return Articulo
     */
    public function setPrecioCosto($precioCosto)
    {
        $this->precioCosto = $precioCosto;

        return $this;
    }

    /**
     * Get precioCosto
     *
     * @return string
     */
    public function getPrecioCosto()
    {
        return $this->precioCosto;
    }

    /**
     * Set gananciaPorcentaje
     *
     * @param string $gananciaPorcentaje
     *
     * @return Articulo
     */
    public function setGananciaPorcentaje($gananciaPorcentaje)
    {
        $this->gananciaPorcentaje = $gananciaPorcentaje;

        return $this;
    }

    /**
     * Get gananciaPorcentaje
     *
     * @return string
     */
    public function getGananciaPorcentaje()
    {
        return $this->gananciaPorcentaje;
    }

    /**
     * Set precioVenta
     *
     * @param string $precioVenta
     *
     * @return Articulo
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
     * Set precioVentaSinIva
     *
     * @param string $precioVentaSinIva
     *
     * @return Articulo
     */
    public function setPrecioVentaSinIva($precioVentaSinIva)
    {
        $this->precioVentaSinIva = $precioVentaSinIva;

        return $this;
    }

    /**
     * Get precioVentaSinIva
     *
     * @return string
     */
    public function getPrecioVentaSinIva()
    {
        return $this->precioVentaSinIva;
    }

    /**
     * Set marca
     *
     * @param string $marca
     *
     * @return Articulo
     */
    public function setmarca($marca)
    {
        $this->marca = $marca;

        return $this;
    }

    /**
     * Get marca
     *
     * @return string
     */
    public function getmarca()
    {
        return $this->marca;
    }

    /**
     * Set iva
     *
     * @param string $iva
     *
     * @return Articulo
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * Get iva
     *
     * @return string
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set precioModifica
     *
     * @param string $precioModifica
     *
     * @return Articulo
     */
    public function setPrecioModifica($precioModifica)
    {
        $this->precioModifica = $precioModifica;

        return $this;
    }

    /**
     * Get precioModifica
     *
     * @return string
     */
    public function getPrecioModifica()
    {
        return $this->precioModifica;
    }

    /**
     * Set ordenTrabajo
     *
     * @param string $ordenTrabajo
     *
     * @return Articulo
     */
    public function setOrdenTrabajo($ordenTrabajo)
    {
        $this->ordenTrabajo = $ordenTrabajo;

        return $this;
    }

    /**
     * Get ordenTrabajo
     *
     * @return string
     */
    public function getOrdenTrabajo()
    {
        return $this->ordenTrabajo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Articulo
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
     * @return Articulo
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
     * @return Articulo
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
     * Set forma
     *
     * @param string $forma
     *
     * @return Articulo
     */
    public function setForma($forma)
    {
        $this->forma = $forma;

        return $this;
    }

    /**
     * Get forma
     *
     * @return string
     */
    public function getForma()
    {
        return $this->forma;
    }

    /**
     * Get marco
     *
     * @return string
     */
    public function getmarco()
    {
        return $this->marco;
    }
    /**
     * Set Marco
     *
     * @param int idMarco
     *
     * @return Articulo
     */
    public function setmarco($marco)
    {
        $this->marco = $marco;

        return $this;
    }
    /**
     * Set colorMarco
     *
     * @param string $colorMarco
     *
     * @return Articulo
     */
    public function setColorMarco($colorMarco)
    {
        $this->colorMarco = $colorMarco;

        return $this;
    }

    /**
     * Get colorMarco
     *
     * @return string
     */
    public function getColorMarco()
    {
        return $this->colorMarco;
    }

    /**
     * Set colorCristal
     *
     * @param string $colorCristal
     *
     * @return Articulo
     */
    public function setColorCristal($colorCristal)
    {
        $this->colorCristal = $colorCristal;

        return $this;
    }

    /**
     * Get colorCristal
     *
     * @return string
     */
    public function getColorCristal()
    {
        return $this->colorCristal;
    }

    /********* DATOS MEDIDAS
     /**
     * Set ancho
     * @param string $ancho
     * @return Articulo
     */
    public function setAncho($ancho)
    {
        $this->ancho = $ancho;

        return $this;
    }
     /**
     * Get ancho
     * @return string
     */
    public function getAncho()
    {
        return $this->ancho;
    }
    /**
    * Set alto
    * @param string $alto
    * @return Articulo
    */
    public function setAlto($alto)
    {
        $this->alto = $alto;

        return $this;
    }
    /**
     * Get alto
     * @return string
     */
    public function getAlto()
    {
        return $this->alto;
    }

    /**
     * Set mayor_distancia
     * @param string $mayor_distancia
     * @return Articulo
     */
    public function setMayorDistancia($mayor_distancia)
    {
        $this->mayor_distancia = $mayor_distancia;

        return $this;
    }
    /**
     * Get mayor_distancia
     * @return string
     */
    public function getMayorDistancia()
    {
        return $this->mayor_distancia;
    }

    /**
     * Set puente
     * @param string puente
     * @return Articulo
     */
    public function setPuente($puente)
    {
        $this->puente = $puente;

        return $this;
    }
    /**
     * Get puente
     * @return string
     */
    public function getPuente()
    {
        return $this->puente;
    }
    /********* DATOS MEDIDAS
    /**
     * Set tipoAro
     *
     * @param int tipoAro
     *
     * @return Articulo
     */
    public function setTipoAro($tipoAro)
    {
        $this->tipoAro = $tipoAro;

        return $this;
    }
    /**
     * Get tipoAro
     *
     * @return string
     *
     */
    public function getTipoAro()
    {
        return $this->tipoAro;
    }
     /**
     * @return string
     *
     */
    public function __toString()
    {
        return $this->descripcion;
    }

    /**
     * Set ultimoComprobante
     *
     * @param \AppBundle\Entity\Comprobante $ultimoComprobante
     *
     * @return Articulo
     */
    public function setUltimoComprobante(\AppBundle\Entity\Comprobante $ultimoComprobante = null)
    {
        $this->ultimoComprobante = $ultimoComprobante;

        return $this;
    }

    /**
     * Get ultimoComprobante
     *
     * @return \AppBundle\Entity\Comprobante
     */
    public function getUltimoComprobante()
    {
        return $this->ultimoComprobante;
    }

    /**
     * Set createdBy
     *
     * @param \AppBundle\Entity\Usuario $createdBy
     *
     * @return Articulo
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
     * @return Articulo
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
