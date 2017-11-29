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
use ZND\USM\OauthBundle\Entity\AccessTokenInterface;
use ZND\USM\OauthBundle\Entity\ClientInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @author mukendi emmanuel <nukendiemmanuel15@gmail.com>
 */
interface UserInterface extends AdvancedUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_BUSINESS = 'ROLE_BUSINESS';

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set confirmationCode
     *
     * @param string $confirmationCode
     * @return self
     */
    public function setConfirmationCode($confirmationCode);

    /**
     * Get confirmationCode
     *
     * @return self
     */
    public function getConfirmationCode();

    /**
     * @return boolean
     */
    public function isIsMailChecked();

    /**
     * @param boolean $is_mail_checked
     */
    public function setIsMailChecked($is_mail_checked);

    /**
     * @return boolean
     */
    public function isIsConfirmed();

    /**
     * @param boolean $is_confirmed
     */
    public function setIsConfirmed($is_confirmed);

    /**
     * Set removed
     *
     * @param boolean $is_removed
     * @return self
     */
    public function setIsRemoved($is_removed = true);

    /**
     * Get removed
     *
     * @return boolean
     */
    public function isRemoved();

    /**
     * Set locked
     *
     * @param bool $is_locked
     */
    public function setIsLocked($is_locked = true);

    /**
     * Get locked
     *
     * @return boolean
     */
    public function isLocked();

    /**
     * Set activated
     *
     * @param boolean $activated
     * @return UserInterface
     */
    public function setIsActivated($activated = true);


    /**
     * {@inheritdoc}
     */
    public function getPhone();

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone);

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * Get activated
     *
     * @return string
     */
    public function isActivated();

    /**
     *
     */
    public function getPlainPassword();

    /**
     *
     */
    public function isSuperPassword();

    /**
     *
     */
    public function isIsAuthenticated();

    /**
     * @param $authenticated
     *
     * @return
     */
    public function setIsAuthenticated($authenticated);
    /**
     * @param $password
     * @return self
     */
    public function setPassword($password);

    /**
     * @param $boolean
     * @return boolean
     */
    public function setSuperPassword($boolean);

    /**
     * @param $password
     * @return self
     */
    public function setPlainPassword($password);

    /**
     * Set created_at
     *
     * @param UserInterface $user
     * @return boolean
     */
    public function isUser(UserInterface $user = null);

    /**
     * Set created_at
     *
     * @param \DateTime|string $createdAt
     * @return UserInterface
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return null|\DateTime
     */
    public function getCreatedAt();

    /**
     * Sets the username.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username);

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical();

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return self
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * @param string|null $salt
     */
    public function setSalt($salt);

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical();

    /**
     * Sets the canonical email.
     *
     * @param string $emailCanonical
     *
     * @return self
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * @param bool $boolean
     *
     * @return self
     */
    public function setEnabled($boolean);

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return self
     */
    public function setSuperAdmin($boolean);

    /**
     * @param $boolean
     *
     * @return $this
     */
    public function setAdmin($boolean);

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken();

    /**
     * Sets the confirmation token.
     *
     * @param string $confirmationToken
     *
     * @return self
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     *
     * @return self
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return int
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time.
     *
     * @param \DateTime $time
     *
     * @return self
     */
    public function setLastLogin(\DateTime $time = null);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function addRole($role);

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return self
     */
    public function removeRole($role);

    /**
     * @return \DateTime
     */
    public function getExpirationDate();

    /**
     * @param \DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate);

    /**
     * @return ClientInterface[]
     */
    public function getClient();

    /**
     * @param ClientInterface $client
     */
    public function addClient(ClientInterface $client);

    /**
     * @param ClientInterface $client
     */
    public function removeClient(ClientInterface $client);

    /**
     * @return AccessTokenInterface[] | ArrayCollection
     */
    public function getAccessTokens();

    /**
     * @param AccessTokenInterface $accessToken
     */
    public function addAccessToken(AccessTokenInterface $accessToken);

    /**
     * @param AccessTokenInterface $accessToken
     */
    public  function removeAccessToken(AccessTokenInterface $accessToken);

    /**
     * @return ProfileInterface
     */
    public function getProfile();

    /**
     * @param ProfileInterface $profile
     */
    public function setProfile(ProfileInterface $profile);

    /**
     * @return LocationInterface
     */
    public function getLocation();

    /**
     * @param LocationInterface $location
     */
    public function setLocation(LocationInterface $location);

    /**
     * @return string
     */
    public function __toString();
}
