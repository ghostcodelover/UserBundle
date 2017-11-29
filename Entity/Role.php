<?php
/**
 * This file is part of the events project.
 * Created on 10/29/17 at 11:45 PM
 *
 * (c) SocialTools <http://social_tools.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZND\USM\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Role
 *
 * @package ZND\USM\UserBundle\Entity
 * @author  Mukendi Emmanuel <ghostcodelover@gmail.com>
 * @ORM\Entity()
 * @ORM\Table(name="znd_usm_user._role")
 */
class Role
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $can_transfer;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $can_charge;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $can_get_back;
}