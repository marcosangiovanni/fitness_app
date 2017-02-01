<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ORM\Table(name="training")
 * @ORM\Table(indexes={@ORM\Index(name="idx_training_position", columns={"position"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainingRepository")
 */
class Training
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Groups({"detail"})
	 */
    private $id;

	/**
     * @ORM\Column(type="integer", length=100)
     */
    private $user_id;

	/**
     * @ORM\Column(type="integer", length=100)
     */
    private $sport_id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=256)
	 * @Groups({"detail"})
	 */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 */
    private $video;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 */
    private $end;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 */
    private $cutoff;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : true})
	 */
    private $is_public;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default" : 0})
	 * @Groups({"detail"})
	 */
    private $price;
	
    /**
	 * @ORM\Column(type="point")
	 * @Groups({"detail"})
	 * @Accessor(getter="getPositionApi",setter="setPositionApi")
	 */
    private $position;

	/**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

	/**
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="trainings")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="id")
	 * @Groups({"detail"})
     */
    private $sport;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="trainings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @MaxDepth(2)
	 * @Groups({"detail"})
	 * @SerializedName("creator")
	 */
    private $user;

	/**
     * @ORM\OneToMany(targetEntity="Invite", mappedBy="training", cascade={"remove"})
     */	
    private $invited;
		
	/**
     * @ORM\OneToMany(targetEntity="Subscribed", mappedBy="training", cascade={"remove"})
	 * @MaxDepth(3)
	 * @Groups({"detail"})
     */	
    private $subscribed;
		
	
	/**********************
	 * GET METHODS        *
	 **********************/

    /**
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string 
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @return \DateTime 
     */
    public function getEnd(){
        return $this->end;
    }

    /**
     * @return \DateTime 
     */
    public function getCutoff(){
        return $this->cutoff;
    }

    /**
     * @return boolean 
     */
    public function getIsPublic(){
        return $this->is_public;
    }

    /**
     * @return float 
     */
    public function getPrice(){
        return $this->price;
    }

    /**
     * @return string 
     */
    public function getPicture(){
        return $this->picture;
    }

    /**
     * @return string 
     */
    public function getVideo(){
        return $this->video;
    }

    /**
     * @return \DateTime 
     */
    public function getStart(){
        return $this->start;
    }

    /**
     * @return point 
     */
    public function getPosition(){
        return $this->position;
    }
	
    /**
     * @return array
	 * This metod is created to handle the serialization of datatype POINT 
     */
    public function getPositionApi(){
        $position = $this->position;
		return array('x' => $position->getX(), 'y' => $position->getY());
    }
	
    /**
     * @return integer
     */
    public function getUserId(){
        return $this->user_id;
    }

    /**
     * @return integer
     */
    public function getSportId(){
        return $this->sport_id;
    }

	/**********************
	 * SET METHODS        *
	 **********************/

    /**
     * @param string $title
     * @return Training
     */
    public function setTitle($title){
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $picture
     * @return Training
     */
    public function setPicture($picture){
        $this->picture = $picture;
        return $this;
    }

    /**
     * @param string $video
     * @return Training
     */
    public function setVideo($video){
        $this->video = $video;
        return $this;
    }

    /**
     * @param \DateTime $start
     * @return Training
     */
    public function setStart($start){
        $this->start = $start;
        return $this;
    }

    /**
     * @param \DateTime $end
     * @return Training
     */
    public function setEnd($end){
        $this->end = $end;
        return $this;
    }

    /**
     * @param \DateTime $cutoff
     * @return Training
     */
    public function setCutoff($cutoff){
        $this->cutoff = $cutoff;
        return $this;
    }

    /**
     * @param boolean $isPublic
     * @return Training
     */
    public function setIsPublic($isPublic){
        $this->is_public = $isPublic;
        return $this;
    }

    /**
     * @param float $price
     * @return Training
     */
    public function setPrice($price){
        $this->price = $price;
        return $this;
    }

    /**
     * @param point $position
     * @return Training
     */
    public function setPosition($position){
        $this->position = $position;
        return $this;
    }

    /**
     * @param integer $userId
     * @return Training
     */
    public function setUserId($userId){
        $this->user_id = $userId;
        return $this;
    }

    /**
     * @param integer $sportId
     * @return Training
     */
    public function setSportId($sportId){
        $this->sport_id = $sportId;
        return $this;
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
     * @param \DateTime $created
     * @return Training
     */
    public function setCreated($created){
        $this->created = $created;
        return $this;
    }

    /**
     * @param \DateTime $updated
     * @return Training
     */
    public function setUpdated($updated){
        $this->updated = $updated;
        return $this;
    }


	/***************************
	 * RELATIONSHIP MANAGEMENT *
	 ***************************/

    /**
     * @param \AppBundle\Entity\Sport $sport
     * @return Training
     */
    public function setSport(\AppBundle\Entity\Sport $sport = null){
        $this->sport = $sport;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Sport 
     */
    public function getSport(){
        return $this->sport;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return Training
     */
    public function setUser(\AppBundle\Entity\User $user = null){
        $this->user = $user;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\User 
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * Constructor
     */
    public function __construct(){
        $this->invited = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subscribed = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param \AppBundle\Entity\Invite $invited
     * @return Training
     */
    public function addInvited(\AppBundle\Entity\Invite $invited){
        $this->invited[] = $invited;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Invite $invited
     */
    public function removeInvited(\AppBundle\Entity\Invite $invited){
        $this->invited->removeElement($invited);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvited(){
        return $this->invited;
    }


    /**
     * @param \AppBundle\Entity\Subscribed $subscribed
     * @return Training
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
}
