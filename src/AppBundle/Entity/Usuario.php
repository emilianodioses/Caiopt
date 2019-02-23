<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 */
class Usuario implements AdvancedUserInterface , \Serializable
{
    public function __construct()
    {
        $this->activo = 1;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->usuario,
            $this->password,
            $this->activo
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->usuario,
            $this->password,
            $this->activo
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
    
    public function getUsername()
    {
        return $this->usuario;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        //hacer un array con todas las funciones disponibles, en vez de todos los roles.
        
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function equals(AdvancedUserInterface $user)
    {
        return $this->id === $user->getId();
    }    
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getActivo();
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=255, unique=true)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \Sucursal
     *
     * @ORM\ManyToOne(targetEntity="Sucursal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id")
     * })
     */
    private $sucursal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_ultimo", type="datetime")
     */
    private $loginUltimo;

    /**
     * @var int
     *
     * @ORM\Column(name="login_cantidad", type="integer")
     */
    private $loginCantidad;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Usuario
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Usuario
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set activo
     *
     * @param integer $activo
     *
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return int
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set ultimoLogin
     *
     * @param \DateTime $ultimoLogin
     *
     * @return Usuario
     */
    public function setUltimoLogin($ultimoLogin)
    {
        $this->ultimoLogin = $ultimoLogin;

        return $this;
    }

    /**
     * Get ultimoLogin
     *
     * @return \DateTime
     */
    public function getUltimoLogin()
    {
        return $this->ultimoLogin;
    }

    /**
     * Set cantidadLogin
     *
     * @param integer $cantidadLogin
     *
     * @return Usuario
     */
    public function setCantidadLogin($cantidadLogin)
    {
        $this->cantidadLogin = $cantidadLogin;

        return $this;
    }

    /**
     * Get cantidadLogin
     *
     * @return int
     */
    public function getCantidadLogin()
    {
        return $this->cantidadLogin;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return Usuario
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
     * @return Usuario
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
     * @return Usuario
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
     * @return Usuario
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
     * Set loginUltimo
     *
     * @param \DateTime $loginUltimo
     *
     * @return Usuario
     */
    public function setLoginUltimo($loginUltimo)
    {
        $this->loginUltimo = $loginUltimo;

        return $this;
    }

    /**
     * Get loginUltimo
     *
     * @return \DateTime
     */
    public function getLoginUltimo()
    {
        return $this->loginUltimo;
    }

    /**
     * Set loginCantidad
     *
     * @param integer $loginCantidad
     *
     * @return Usuario
     */
    public function setLoginCantidad($loginCantidad)
    {
        $this->loginCantidad = $loginCantidad;

        return $this;
    }

    /**
     * Get loginCantidad
     *
     * @return integer
     */
    public function getLoginCantidad()
    {
        return $this->loginCantidad;
    }

    /**
     * Set sucursal
     *
     * @param \AppBundle\Entity\Sucursal $sucursal
     *
     * @return Usuario
     */
    public function setSucursal(\AppBundle\Entity\Sucursal $sucursal = null)
    {
        $this->sucursal = $sucursal;

        return $this;
    }

    /**
     * Get sucursal
     *
     * @return \AppBundle\Entity\Sucursal
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

}
