<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RolFuncion
 *
 * @ORM\Table(name="rol_funcion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RolFuncionRepository")
 */
class RolFuncion
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
     * @var \Rol
     *
     * @ORM\ManyToOne(targetEntity="Rol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rol_id", referencedColumnName="id")
     * })
     */
    private $rol;

    /**
     * @var \Funcion
     *
     * @ORM\ManyToOne(targetEntity="Funcion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="funcion_id", referencedColumnName="id")
     * })
     */
    private $funcion;

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
     * Set rol
     *
     * @param \AppBundle\Entity\Rol $rol
     *
     * @return RolFuncion
     */
    public function setRol(\AppBundle\Entity\Rol $rol = null)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return \AppBundle\Entity\Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set funcion
     *
     * @param \AppBundle\Entity\Funcion $funcion
     *
     * @return RolFuncion
     */
    public function setFuncion(\AppBundle\Entity\Funcion $funcion = null)
    {
        $this->funcion = $funcion;

        return $this;
    }

    /**
     * Get funcion
     *
     * @return \AppBundle\Entity\Funcion
     */
    public function getFuncion()
    {
        return $this->funcion;
    }
}
