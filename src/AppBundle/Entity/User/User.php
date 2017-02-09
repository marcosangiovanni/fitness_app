<?php

namespace AppBundle\Entity\User;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user_user")
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Groups({"detail"})
	 * @Type("integer")
     */
    protected $id;


	/*************************
	 * NEW DEFINED FIELDS    *
	 *************************/

	/**
     * @Vich\UploadableField(mapping="training_image", fileNameProperty="picture")
     * @var File
     */
    private $imageFile;
	
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $video;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $city;


	/***********************************************
	 * FIELDS INHERITED FROM SonataUserBundle      *
	 * ADDED IN THIS PLACE TO DEFINE SERIALIZATION *
	 ***********************************************/

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    protected $firstname;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    protected $lastname;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    protected $email;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    protected $phone;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    protected $gender;

    /**
	 * @Groups({"detail"})
	 * @Type("DateTime<'Y-m-d'>")
	 */
    protected $dateOfBirth;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
	protected $facebookUid;



	/**********************************
	 * FIELDS TO DEFINE RELATIONSHIPS *
	 **********************************/

	/**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Sport", inversedBy="users")
     * @ORM\JoinTable(name="ass_user_sport")
	 * @SerializedName("sports")
	 * @MaxDepth(2)
	 * @Groups({"detail"})
	 * @Type("ArrayCollection")
	 */
    private $sports;
	
	/**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FacebookFriend", mappedBy="user", cascade={"remove"})
	 * @SerializedName("facebook_friends")
	 * @Groups({"detail"})
	 * @Type("ArrayCollection")
	 */
    private $friends;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="ass_user_friend",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
	 * @Groups({"detail"})
     * @MaxDepth(2)
	 * @SerializedName("friends")
	 * @Type("ArrayCollection")
     */
    private $myFriends;

	/**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
	 */
    private $friendsWithMe;

	/**
     * Variable to store trainings
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Training", mappedBy="user", cascade={"remove"})
	 * @SerializedName("created_trainings")
	 * @Groups({"detail"})
	 * @ORM\OrderBy({"start" = "DESC"})
	 * @Type("ArrayCollection")
     */
    private $trainings;
	
	/**
     * Variable to store trainings to whom the user is subscribed
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subscribed", mappedBy="user", cascade={"remove"})
	 * @SerializedName("associated_trainings")
	 * @Groups({"detail"})
	 * @Type("ArrayCollection")
     */
    private $subscribed;
	
    public function __construct(){
        parent::__construct();
		$this->friends = new ArrayCollection();
		$this->sports = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
        $this->invited = new ArrayCollection();
        $this->subcribed = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }


	/**********************
	 * SET METHODS        *
	 **********************/
	 
	/* generic setter method*/
	public function __set($property, $value){
        $this->$property = $value;
        return $this;
    }
	 
	public function setPicture($picture){
        $this->picture = $picture;
        return $this;
    }
	
    public function setVideo($video){
        $this->video = $video;
        return $this;
    }
	
    public function setCity($city){
        $this->city = $city;
        return $this;
    }
	
    public function setDob($dob){
        $this->dob = $dob;
        return $this;
    }

    
	/**********************
	 * GET METHODS        *
	 **********************/
	 
	/* generic getter method*/
	public function __get($property){
        return $this->$property;
    }
	 
	public function getPicture(){
        return $this->picture;
    }

    public function getVideo(){
        return $this->video;
    }

    public function getCity(){
        return $this->city;
    }

    public function getDob(){
        return $this->dob;
    }
	
	
	/************************
	 * GENERIC RELATIONSHIP *
	 ************************/
	
	public function add($property,$obj){
		$this->$property->add($obj);
        return $this;
    }

	public function remove($property,$obj){
		$this->$property->removeElement($obj);
    }

	/*************************
	 * FB FRIENDS MANAGEMENT *
	 *************************/

    /**
     * Add friends
     *
     * @param \AppBundle\Entity\FacebookFriend $friends
     * @return User
     */
    public function addFriend(\AppBundle\Entity\FacebookFriend $friends){
        $this->friends[] = $friends;
        return $this;
    }

    /**
     * Remove friends
     *
     * @param \AppBundle\Entity\FacebookFriend $friends
     */
    public function removeFriend(\AppBundle\Entity\FacebookFriend $friends){
        $this->friends->removeElement($friends);
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends(){
        return $this->friends;
    }


	/********************
	 * SPORT MANAGEMENT *
	 ********************/

    /**
     * Add sports
     *
     * @param \AppBundle\Entity\Sport $sports
     * @return User
     */
    public function addSport(\AppBundle\Entity\Sport $sports){
    	$sports->addUser($this);
		$this->sports[] = $sports;
        return $this;
    }

    /**
     * Remove sports
     *
     * @param \AppBundle\Entity\Sport $sports
     */
    public function removeSport(\AppBundle\Entity\Sport $sports){
        $this->sports->removeElement($sports);
    }

    /**
     * Get sports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSports(){
        return $this->sports;
    }


	/**************************
	 * APP FRIENDS MANAGEMENT *
	 **************************/

    /**
     * Add friendsWithMe
     *
     * @param \User $friendsWithMe
     * @return User
     */
    public function addFriendsWithMe(User $friendsWithMe){
        $this->friendsWithMe[] = $friendsWithMe;
        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param \User $friendsWithMe
     */
    public function removeFriendsWithMe(User $friendsWithMe){
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriendsWithMe(){
        return $this->friendsWithMe;
    }

    /**
     * Add myFriends
     *
     * @param \User $myFriends
     * @return User
     */
    public function addMyFriend(User $myFriends){
        $this->myFriends[] = $myFriends;
        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param \AppBundle\Entity\User\User $myFriends
     */
    public function removeMyFriend(\AppBundle\Entity\User\User $myFriends){
        $this->myFriends->removeElement($myFriends);
    }

    /**
     * Get myFriends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMyFriends(){
        return $this->myFriends;
    }


	/****************************
	 * TIMESTAMPABLE MANAGEMENT *
	 ****************************/
	 
    /**
     * @return \DateTime 
     */
    public function getCreated(){
        return $this->created;
    }

    /**
     * @return \DateTime 
     */
    public function getUpdated(){
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated($updated){
        $this->updated = $updated;
        return $this;
    }

    /**
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created){
        $this->created = $created;
        return $this;
    }


	/***************************
	 * RELATIONSHIP MANAGEMENT *
	 ***************************/
	 
    /**
     * @param \AppBundle\Entity\Subscribed $subscribed
     * @return User
     */
    public function addSubscribed(\AppBundle\Entity\Subscribed $subscribed){
        $this->subscribed[] = $subscribed;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Subscribed $subscribed
     */
    public function removeSubscribed(\AppBundle\Entity\Subscribed $subscribed){
        $this->subscribed->removeElement($subscribed);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribed(){
        return $this->subscribed;
    }
	
    /**
     * @param \AppBundle\Entity\Training $training
     * @return User
     */
    public function addTraining(\AppBundle\Entity\Training $training){
        $this->trainings[] = $training;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Training $training
     */
    public function removeTraining(\AppBundle\Entity\Training $training){
        $this->trainings->removeElement($training);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrainings(){
        return $this->trainings;
    }


	/***************************
	 * IMAGE UPLOAD MANAGEMENT *
	 ***************************/
	
	/*
	 *  Doctrine only upload file if any field is modified 
	 */
	public function setImageFile(File $image = null){
        $this->imageFile = $image;
        if ($image) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(){
        return $this->imageFile;
    }
	

}
