<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity 
 * @ORM\EntityListeners({"AppBundle\EventListener\FacebookFriendListener"})
 * @ORM\Table(name="facebook_friend")
 */
class FacebookFriend
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Groups({"detail"})
	 * @Type("integer")
	 */
    private $id;

	/**
     * @ORM\Column(type="integer", length=100)
	 * @Type("integer")
     */
    private $user_id;

	/**
     * @ORM\Column(type="string", length=100)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $name;

	/**
     * @ORM\Column(type="string", length=1256)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $picture;

    /**
     * @ORM\Column(type="string", length=100)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $facebook_uid;

	/**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User\User", inversedBy="friends")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
	
	/* Recupero il nome dal modello */
	public function __toString()
    {
        return (string)$this->name;
    }

    public function getId(){
        return $this->id;
    }

    public function setUserId($userId){
        $this->user_id = $userId;
        return $this;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function setPicture($picture){
        $this->picture = $picture;
        return $this;
    }

    public function getPicture(){
        return $this->picture;
    }

    public function getName(){
        return $this->name;
    }

    public function setFacebookUid($facebookUid){
        $this->facebook_uid = $facebookUid;
        return $this;
    }

    public function getFacebookUid(){
        return $this->facebook_uid;
    }

	/**
     * Add users
     *
     * @param \AppBundle\Entity\User\User $users
     * @return Sport
     */
    public function addUser(\AppBundle\Entity\User\User $users){
    	if (!$this->users->contains($users)) {
			$this->users->add($users);
		}
        return $this;
    }

	/**
     * Remove users
     *
     * @param \AppBundle\Entity\User\User $users
     */
    public function removeUser(\AppBundle\Entity\User\User $user){
    	$user->removeFriend($this);
        $this->users->removeElement($user);
    }
	
    /**
     * Set user
     *
     * @param AppBundle\Entity\User\User $user
     * @return FacebookFriend
     */
    public function setUser(\AppBundle\Entity\User\User $user = null){
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User\User
     */
    public function getUser(){
        return $this->user;
    }
}
