<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientePago
 *
 * @ORM\Table(name="cliente_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientePagoRepository")
 */
class ClientePago
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
     * @var \Recibo
     *
     * @ORM\ManyToOne(targetEntity="Recibo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recibo_id", referencedColumnName="id")
     * })
     */
    private $recibo;
    
    /**
     * @var \PagoTipo
     *
     * @ORM\ManyToOne(targetEntity="PagoTipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pago_tipo_id", referencedColumnName="id")
     * })
     */
    private $pagoTipo;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=16, scale=2)
     */
    private $total;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return ClientePago
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
     * Set activo
     *
     * @param boolean $activo
     *
     * @return ClientePago
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return ClientePago
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
     * @return ClientePago
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
     * @return ClientePago
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
     * @return ClientePago
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
     * Set recibo
     *
     * @param \AppBundle\Entity\Recibo $recibo
     *
     * @return ClientePago
     */
    public function setRecibo(\AppBundle\Entity\Recibo $recibo = null)
    {
        $this->recibo = $recibo;

        return $this;
    }

    /**
     * Get recibo
     *
     * @return \AppBundle\Entity\Recibo
     */
    public function getRecibo()
    {
        return $this->recibo;
    }

    /**
     * Set pagoTipo
     *
     * @param \AppBundle\Entity\PagoTipo $pagoTipo
     *
     * @return ClientePago
     */
    public function setPagoTipo(\AppBundle\Entity\PagoTipo $pagoTipo = null)
    {
        $this->pagoTipo = $pagoTipo;

        return $this;
    }

    /**
     * Get pagoTipo
     *
     * @return \AppBundle\Entity\PagoTipo
     */
    public function getPagoTipo()
    {
        return $this->pagoTipo;
    }
}
