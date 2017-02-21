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
use JMS\Serializer\Annotation\ReadOnly;

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


	/**********************
	 * FIELDS FOR INVOICE *
	 **********************/

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $fiscal_city;

    /**
     * @ORM\Column(type="string", length=1255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $fiscal_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $fiscal_cap;

    /**
     * @ORM\Column(type="string", length=1255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $fiscal_house_number;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $fiscal_code;

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
	 * @var \Date
	 */
    protected $dateOfBirth;

    /**
	 * @Groups({"detail"})
	 * @Type("string")
	 */
	protected $facebookUid;

    /**
	 * @var \Date
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    protected $last_facebook_refresh;

	/*********************************************************
	 * FEEDBACK FIELDS (calculated from subscribed entities) *
	 *********************************************************/
	 
    /**
	 * @ORM\Column(type="string", nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
	protected $feedback_avg;

    /**
	 * @ORM\Column(type="integer", nullable=true)
	 * @Groups({"detail"})
	 * @Type("integer")
	 */
    protected $feedback_num;

    
	/**********************************
	 * FIELDS TO DEFINE RELATIONSHIPS *
	 **********************************/

 	/**
     * Variable to store trainings to whom the user is subscribed
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subscribed", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @SerializedName("associated_trainings")
	 * @Groups({"detail"})
	 * @Type("ArrayCollection<AppBundle\Entity\Subscribed>")
	 * @MaxDepth(4)
	 * @ReadOnly
     */
    private $subscribed;
	
	/**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Sport", inversedBy="users", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="ass_user_sport")
	 * @SerializedName("sports")
	 * @MaxDepth(2)
	 * @Groups({"detail"})
	 * @Type("ArrayCollection<AppBundle\Entity\Sport>")
	 * @ReadOnly
	 */
    private $sports;
	
	/**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Sport", inversedBy="users", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="ass_user_sport_trained")
	 * @SerializedName("sports_trained")
	 * @MaxDepth(2)
	 * @Groups({"detail"})
	 * @Type("ArrayCollection<AppBundle\Entity\Sport>")
	 * @ReadOnly
	 */
    private $sports_trained;
	
	/**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FacebookFriend", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @SerializedName("facebook_friends")
	 * @Groups({"detail"})
	 * @Type("ArrayCollection<AppBundle\Entity\FacebookFriend>")
	 * @ReadOnly
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
	 * @Type("ArrayCollection<AppBundle\Entity\User\User>")
	 * @ReadOnly
     */
    private $myFriends;

	/**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
	 */
    private $friendsWithMe;

	/**
     * Variable to store trainings
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Training", mappedBy="user", cascade={"persist", "remove"})
	 * @SerializedName("created_trainings")
	 * @Groups({"detail"})
	 * @ORM\OrderBy({"start" = "DESC"})
	 * @Type("ArrayCollection<AppBundle\Entity\Training>")
	 * @MaxDepth(3)
	 * @ReadOnly
     */
    private $trainings;
	
    public function __construct(){
        parent::__construct();
		$this->friends = new ArrayCollection();
		$this->sports = new ArrayCollection();
		$this->sports_trained = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
        $this->invited = new ArrayCollection();
        $this->subscribed = new ArrayCollection();
        $this->trainings = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId(){
        return $this->id;
    }

	/**********************
	 * SET METHODS        *
	 **********************/
	 
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

    public function setFiscalCity($fiscal_city){
        $this->fiscal_city = $fiscal_city;
        return $this;
    }

    public function setFiscalAddress($fiscal_address){
        $this->fiscal_address = $fiscal_address;
        return $this;
    }

    public function setFiscalCap($fiscal_cap){
        $this->fiscal_cap = $fiscal_cap;
        return $this;
    }

    public function setFiscalHouseNumber($fiscal_house_number){
        $this->fiscal_house_number = $fiscal_house_number;
        return $this;
    }

    public function setFiscalCode($fiscal_code){
        $this->fiscal_code = $fiscal_code;
        return $this;
    }
    
    public function setFeedbackAvg($feedback_avg){
        $this->feedback_avg = $feedback_avg;
        return $this;
    }
    
    public function setFeedbackNum($feedback_num){
        $this->feedback_num = $feedback_num;
        return $this;
    }
    
	/**********************
	 * GET METHODS        *
	 **********************/
	 
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
	
	public function getFiscalCity(){
        return $this->fiscal_city;
    }

    public function getFiscalAddress(){
        return $this->fiscal_address;
    }

    public function getFiscalCap(){
        return $this->fiscal_cap;
    }

    public function getFiscalHouseNumber(){
        return $this->fiscal_house_number;
    }

    public function getFiscalCode(){
        return $this->fiscal_code;
    }
	
    public function getFeedbackAvg(){
        return $this->feedback_avg;
    }
    
    public function getFeedbackNum(){
        return $this->feedback_num;
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
    public function addFriend(\AppBundle\Entity\FacebookFriend $friend){
    	$friend->setUser($this);
        $this->friends[] = $friend;
        return $this;
    }

    /**
     * Remove friends
     *
     * @param \AppBundle\Entity\FacebookFriend $friends
     */
    public function removeFriend(\AppBundle\Entity\FacebookFriend $friend){
    	$friend->setUser(null);
        $this->friends->removeElement($friend);
    }
	
    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends(){
        return $this->friends;
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setFriends($friends){
        $this->friends[] = $friends;
		return $this;
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
    public function addSport(\AppBundle\Entity\Sport $sport){
		$this->sports[] = $sport;
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

    /**
     * Get user
     *
     * @return  \AppBundle\Entity\user\User $user
     */
    public function setSports($sports){
    	$this->sports[] = $sports;
        return $this;
    }



    /**
     * Add sports_trained
     *
     * @param \AppBundle\Entity\SportTrained $sports_trained
     * @return User
     */
    public function addSportTrained(\AppBundle\Entity\Sport $sports_trained){
		$this->sports_trained[] = $sports_trained;
        return $this;
    }

    /**
     * Remove sports
     *
     * @param \AppBundle\Entity\Sport $sports
     */
    public function removeSportTrained(\AppBundle\Entity\Sport $sports_trained){
        $this->sports_trained->removeElement($sports_trained);
    }

    /**
     * Get sports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSportsTrained(){
        return $this->sports_trained;
    }

    /**
     * Get user
     *
     * @return  \AppBundle\Entity\user\User $user
     */
    public function setSportsTrained($sports_trained){
    	$this->sports_trained = $sports_trained;
        return $this;
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
    	$subscribed->setUser($this);
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
    	$training->setUser($this);
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
	
	/*********************
	 * CONFIG MANAGEMENT *
	 ********************/
	
	/**
     * @return boolean
     */
    public function hasToBeFacebookFriendsListRefreshed($config){
    	$last_refresh_date = $this->getLastFacebookRefresh();
		if($last_refresh_date){
			$now = new \DateTime();
			$next_refresh_date = $last_refresh_date->add(new \DateInterval('P'.$config->getValue().'D'));
			//Return true if next refresh date is elapsed
	        return $now > $next_refresh_date;
		}
		//Se non è settata last_refresh_date allora aggiorniamo i dati di facebook
		return true;
    }

	public function setLastFacebookRefresh($last_facebook_refresh){
        $this->last_facebook_refresh = $last_facebook_refresh;
        return $this;
    }
    
	public function getLastFacebookRefresh(){
        return $this->last_facebook_refresh;
    }
	

}
