<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZND\USM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\LocationRepository")
 * @ORM\Table(name="znd_usm_user._location")
 */
class Location implements LocationInterface
{
        /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
     protected $id;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Groups({"api_location"})
     */
    protected $country;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $state;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $town;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $commune;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $avenue;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $street;

    /**
     * @var integer
     * @ORM\Column(nullable=true)
     * 
     * @Groups({"api_location"})
     */
    protected $streetNumber;

    /**
     * @var float
     * @ORM\Column(type="decimal", nullable=true)
     * @Groups({"api_location"})
     */
    protected $latitude;

    /**
     * @var float
     * @ORM\Column(type="decimal", nullable=true)
     * @Groups({"api_location"})
     */
    protected $longitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="postal", type="integer", nullable =true)
     * @Groups({"api_location"})
     */
    protected $postal;

    /**
     * @var UserInterface
     * @ORM\OneToOne(targetEntity="ZND\USM\UserBundle\Entity\User", cascade={"persist"}, inversedBy="location")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $user;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param string $commune
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;
    }

    /**
     * @return string
     */
    public function getAvenue()
    {
        return $this->avenue;
    }

    /**
     * @param string $avenue
     */
    public function setAvenue($avenue)
    {
        $this->avenue = $avenue;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return int
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param int $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return int
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param int $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return int
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param int $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return int
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * @param int $postal
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
    }

    /**
     * @return \ZND\USM\UserBundle\Entity\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
