<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parametro
 *
 * @ORM\Table(name="parametro")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParametroRepository")
 */
class Parametro
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
     * @ORM\Column(name="codigo", type="string", length=255, unique=true)
     */
    private $codigo;
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, unique=true)
     */
    private $descripcion;
    /**
     * @var string
     *
     * @ORM\Column(name="valorTexto", type="string", length=255, unique=true, nullable=true)
     */
    private $valorTexto;
    /**
     * @var int
     *
     * @ORM\Column(name="valorNro", type="integer")
     */
    private $valorNro;
    /**
     * @var string
     *
     * @ORM\Column(name="valorImporte", type="decimal", precision=16, scale=2)
     */
    private $valorImporte = '0';
    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var \Parametro
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
     * @var \Parametro
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
     * @return Parametro
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Parametro
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
     * Set valorTexto
     *
     * @param string $valorTexto
     *
     * @return Parametro
     */
    public function setValorTexto($valorTexto)
    {
        $this->valorTexto = $valorTexto;

        return $this;
    }

    /**
     * Get valorTexto
     *
     * @return string
     */
    public function getValorTexto()
    {
        return $this->valorTexto;
    }


    /**
     * Set valorNro
     *
     * @param integer $valorNro
     *
     * @return Parametro
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
     * Set valorImporte
     *
     * @param string $valorImporte
     *
     * @return Parametro
     */
    public function setValorImporte($valorImporte)
    {
        $this->valorImporte = $valorImporte;

        return $this;
    }
    /**
     * Get valorImporte
     *
     * @return string
     */
    public function getValorImporte()
    {
        return $this->valorImporte;
    }
    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Parametro
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
     * @return Parametro
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
     * @return Parametro
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
     * @param \AppBundle\Entity\Parametro $createdBy
     *
     * @return Parametro
     */
    public function setCreatedBy(\AppBundle\Entity\Parametro $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\Parametro
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
     * @return Parametro
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
