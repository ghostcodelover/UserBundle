<?php
/******************************************************************************
 *   This file is part of the EventsCoreBundle package.                       *
 *                                                                            *
 *   (c) Events <http://events.cd/>                                           *
 *                                                                            *
 *   For the full copyright and license information, please view the LICENSE  *
 *   file that was distributed with this source code.                         *
 ******************************************************************************/

namespace ZND\USM\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JS;

/**
 * @ORM\Entity(repositoryClass="ZND\USM\UserBundle\Repository\ProfileRepository")
 * @ORM\Table(name="znd_usm_user._profile")
 * @ORM\HasLifecycleCallbacks
 */
class Profile implements ProfileInterface
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="guid", type="string", nullable=true)
     */
    protected $guid;

    /**
     * @var integer
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender;

    /**
     * @var  \DateTime
     *
     * @ORM\Column(name="birth_day", type="date", nullable=true)
     */
    protected $birth_day;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var UserInterface
     * @ORM\OneToOne(
     *     targetEntity="ZND\USM\UserBundle\Entity\User", inversedBy="profile" ,cascade={"persist"}
     *
     * )
     * @ORM\JoinColumn(name="user_id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getGuId()
    {
        return !empty($this->guid)? $this->guid : $this->user->getUsername();
    }

    protected function updateGuId(){
        $this->guid = $this->getFirstName().'.'.$this->getLastName().($this->getPosition()!=0?'.'.$this->position:'');
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = (int)$position ==0 ? $position: 1+ (int) $position;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
         $this->first_name= $first_name;
         is_string($first_name)?$this->updateGuId():null;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
         $this->last_name= $last_name;
         is_string($last_name)?$this->updateGuId():null;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDay()
    {
        return $this->birth_day;
    }

    /**
     * @param string $birth_day
     */
    public function setBirthDay($birth_day)
    {
        $this->birth_day = $birth_day;
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
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $user->setProfile($this);
        $this->user = $user;
    }
}
