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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZND\USM\OauthBundle\Entity\AccessTokenInterface;
use ZND\USM\OauthBundle\Entity\ClientInterface;
use JMS\Serializer\Annotation as JS;
use Symfony\Component\Validator\Constraints as CST;

/**
 * @ORM\Entity(repositoryClass="ZND\USM\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="znd_usm_user._user")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=255, nullable =false)
     * @JS\Groups({"user", "admin"})
     */
    protected $username;

    /**
     * @var string
     */
    protected $first_name;

    /**
     * @var string
     */
    protected $last_name;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone", type="integer", nullable =true)
     * @JS\Groups({"user", "admin"})
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="usernameCanonical", type="string", length=255, nullable=false,unique=true)
     * @JS\Groups({"admin"})
     */
    protected $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @JS\Groups({"user", "admin"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="emailcanonical", type="string", length=255, nullable=false,unique=true)
     */
    protected $emailCanonical;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", nullable=false)
     * @JS\Groups({"admin"})
     */
    protected $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     * @var string
     *
     * @ORM\Column(name="confirmationtoken", type="string", length=255, nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $confirmationToken;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", length=255, nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmationcode", type="string", length=255, nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $confirmationCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_removed", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $is_removed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_confirmed", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $is_confirmed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $is_locked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_mail_checked", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $is_mail_checked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_activated", type="boolean", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $is_activated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_authenticated", type="boolean", nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $is_authenticated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $createdAt;

    /**
     * @var string
     * @JS\Exclude
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @JS\Groups({"user", "admin"})
     */
    protected $password;

    /**
     * @var string
     * @JS\Groups({"admin"})
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     * @JS\Groups({"admin"})
     */
    protected $passwordRequestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     * @JS\Groups({"user", "admin"})
     */
    protected $expirationDate;

    /**
     * @var ArrayCollection
     * @JS\Exclude
     * @ORM\OneToMany(targetEntity="ZND\USM\OauthBundle\Entity\Client", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $client;

    /**
     * @var ArrayCollection
     * @JS\Exclude
     * @ORM\OneToMany(targetEntity="ZND\USM\OauthBundle\Entity\AccessToken", mappedBy="user", cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    protected $accessToken;

    /**
     * @var ProfileInterface
     *
     * @ORM\OneToOne(targetEntity="ZND\USM\UserBundle\Entity\Profile", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    protected $profile;

    /**
     * @var LocationInterface
     * @ORM\OneToOne(targetEntity="ZND\USM\UserBundle\Entity\Location",cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     */
    protected $location;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->is_activated = false;
        $this->is_mail_checked = false;
        $this->is_removed = false;
        $this->is_confirmed= false;
        $this->is_locked= false;
        $this->enabled = false;
        $this->roles = [];
        $this->client= new ArrayCollection();
        $this->accessToken= new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperPassword()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperPassword($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }



    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
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
        $this->first_name = $first_name;
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
        $this->last_name = $last_name;
    }


    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {   $this->phone= $phone;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean=true)
    {
        $this->enabled = (bool) $boolean;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
        return $this->setAdmin($boolean);
    }

    /**
     * @param $boolean
     *
     * @return $this
     */
    public function setAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_ADMIN);
        } else {
            $this->removeRole(static::ROLE_ADMIN);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isIsConfirmed()
    {
        return $this->is_confirmed;
    }

    /**
     * @param boolean $is_confirmed
     */
    public function setIsConfirmed($is_confirmed)
    {
        $this->is_confirmed = $is_confirmed;
    }

    /**
     * @return boolean
     */
    public function isIsMailChecked()
    {
        return $this->is_mail_checked;
    }

    /**
     * @param boolean $is_mail_checked
     */
    public function setIsMailChecked($is_mail_checked)
    {
        $this->is_mail_checked = (bool)$is_mail_checked;
    }

    /**
     * @return boolean
     */
    public function isIsAuthenticated()
    {
        return $this->is_authenticated;
    }

    /**
     * @param boolean $is_authenticated
     */
    public function setIsAuthenticated($is_authenticated)
    {
        $this->is_authenticated = $is_authenticated;
    }


    /**
     * {@inheritdoc}
     */
    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRemoved($is_removed = true)
    {
        $this->is_removed = $is_removed;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRemoved()
    {
        return $this->is_removed;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsLocked($is_locked = true)
    {
        $this->is_locked = $is_locked;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->is_locked;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActivated($is_activated = true)
    {
        $this->is_activated = $is_activated;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActivated()
    {
        return $this->is_activated;
    }
    /**
     * {@inheritdoc}
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

     /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $date = null)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return ClientInterface[] | ArrayCollection
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     */
    public function addClient(ClientInterface $client)
    {
        if(!$this->client->contains($client)){
            $this->client->add($client);
        }
    }

    /**
     * @param ClientInterface $client
     */
    public function removeClient(ClientInterface $client)
    {
        if($this->client->contains($client)){
            $this->client->removeElement($client);
        }
    }

    /**
     * @return AccessTokenInterface[] | ArrayCollection
     */
    public function getAccessTokens()
    {
        return $this->accessToken;
    }

    /**
     * @param AccessTokenInterface $accessToken
     */
    public function addAccessToken(AccessTokenInterface $accessToken)
    {   if (!$this->accessToken->contains($accessToken)){
              $this->accessToken->add($accessToken);
            }
    }

    /**
     * @param AccessTokenInterface $accessToken
     */
    public  function removeAccessToken(AccessTokenInterface $accessToken){
        if ($this->accessToken->contains($accessToken)){
            $this->accessToken->removeElement($accessToken);
        }
    }

    /**
     * @return ProfileInterface
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param ProfileInterface $profile
     */
    public function setProfile(ProfileInterface $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return LocationInterface
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param LocationInterface $location
     */
    public function setLocation(LocationInterface $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }
}
