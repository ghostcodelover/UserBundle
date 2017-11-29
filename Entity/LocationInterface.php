<?php
/**
 * Created by PhpStorm.
 * User: localgit
 * Date: 6/17/17
 * Time: 12:11 PM
 */

namespace ZND\USM\UserBundle\Entity;

/**
 * Interface LocationInterface
 *
 * @package ZND\USM\UserBundle\Entity
 * @author  Mukendi Emmanuel <ghostcodelover@gmail.com>
 */
interface LocationInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getState();
    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getTown();

    /**
     * @param string $town
     */
    public function setTown($town);

    /**
     * @return string
     */
    public function getCommune();

    /**
     * @param string $commune
     */
    public function setCommune($commune);

    /**
     * @return string
     */
    public function getAvenue();

    /**
     * @param string $avenue
     */
    public function setAvenue($avenue);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $street
     */
    public function setStreet($street);

    /**
     * @return int
     */
    public function getStreetNumber();

    /**
     * @param int $streetNumber
     */
    public function setStreetNumber($streetNumber);

    /**
     * @return int
     */
    public function getLatitude();

    /**
     * @param int $latitude
     */
    public function setLatitude($latitude);

    /**
     * @return int
     */
    public function getLongitude();

    /**
     * @param int $longitude
     */
    public function setLongitude($longitude);
    /**
     * @return int
     */
    public function getPostal();

    /**
     * @param int $postal
     */
    public function setPostal($postal);
    /**
     * @return \ZND\USM\UserBundle\Entity\UserInterface
     */
    public function getUser();

    /**
     * @param \ZND\USM\UserBundle\Entity\UserInterface $user
     */
    public function setUser(UserInterface $user);
}