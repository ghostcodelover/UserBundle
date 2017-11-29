<?php

namespace ZND\USM\UserBundle\Form\Factory;

use ZND\SIM\ApiBundle\Form\Factory\ApiFormFactoryInterface;
use Symfony\Component\Form\FormInterface;

interface UserFormFactoryInterface extends ApiFormFactoryInterface
{
    /**
     * @param null  $data
     * @param array $options
     *
     * @return bool|FormInterface
     */
    public function createUser($data = null, array $options = array());

    /**
     * @param null  $data
     * @param array $options
     *
     * @return bool|FormInterface
     */
    public function createProfile($data = null, array $options = array());
}