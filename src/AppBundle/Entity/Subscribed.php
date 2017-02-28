<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Table(name="ass_user_training_subscribed",uniqueConstraints={@ORM\UniqueConstraint(name="subscribed_training_id_user_id_IDX", columns={"user_id", "training_id"})})
 * @ORM\Entity @ORM\EntityListeners({"AppBundle\EventListener\SubscribedListener"})
 */
 
class Subscribed
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Groups({"detail"})
	 * @Type("integer")
     */
    private $id;

	/**
     * @ORM\Column(type="integer", length=100)
     */
    private $user_id;

	/**
     * @ORM\Column(type="integer", length=100)
     */
    private $training_id;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : NULL})
	 */
    private $is_notified_start;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : NULL})
	 */
    private $is_notified_end;

	/**
     * @ORM\Column(type="float", nullable=true)
	 * @Groups({"detail"})
	 * @Type("float")
	 */ 
    private $feedback;

	/**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : false})
	 * @Groups({"detail"})
	 * @Type("boolean")
	 */ 
    private $asked_feedback;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User\User", inversedBy="subscribed", cascade={"persist"})) 
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @Groups({"detail"})
	 * @Type("AppBundle\Entity\User\User")
	 */ 
    private $user;

    /** 
     * @ORM\ManyToOne(targetEntity="Training", inversedBy="subscribed", cascade={"persist"})) 
	 * @ORM\JoinColumn(name="training_id", referencedColumnName="id")
	 * @Groups({"detail"})
	 * @Type("AppBundle\Entity\Training")
	 */ 
    private $training; 

	public function __construct(User $user = null, Training $training = null, $feedback = null) { 
        $this->user = $user; 
        $this->training = $training; 
        $this->feedback = $feedback; 
	}
		
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
     * @return boolean 
     */
    public function getIsNotifiedStart(){
        return $this->is_notified_start;
    }

    /**
     * @return boolean 
     */
    public function getIsNotifiedEnd(){
        return $this->is_notified_end;
    }

    /**
     * Get asked_feedback
     * @return boolean
     */
    public function getAskedFeedback(){
        return $this->asked_feedback;
    }

    /**
     * Get userId
     * @return integer
     */
    public function getUserId(){
        return $this->user_id;
    }

    /**
     * @return integer
     */
    public function getTrainingId(){
        return $this->training_id;
    }

    /**
     * @return float
     */
    public function getFeedback(){
        return $this->feedback;
    }

	/**********************
	 * SET METHODS        *
	 **********************/

	/**
     * @param integer $userId
     * @return Invite
     */
    public function setUserId($userId){
        $this->user_id = $userId;
        return $this;
    }

	/**
     * @param integer $userId
     * @return Invite
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

	/**
     * @param boolean $asked_feedback
     * @return Subscribed
     */
    public function setAskedFeedback($asked_feedback){
        $this->asked_feedback = $asked_feedback;
        return $this;
    }

    /**
     * @param boolean $isPublic
     * @return Training
     */
    public function setIsNotifiedStart($isNotified){
        $this->is_notified_start = $isNotified;
        return $this;
    }

    /**
     * @param boolean $isPublic
     * @return Training
     */
    public function setIsNotifiedEnd($isNotified){
        $this->is_notified_end = $isNotified;
        return $this;
    }

    /**
     * @param integer $trainingId
     * @return Invite
     */
    public function setTrainingId($trainingId){
        $this->training_id = $trainingId;
        return $this;
    }

    /**
     * @param float $feedback
     * @return Invite
     */
    public function setFeedback($feedback){
        $this->feedback = $feedback;
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
     * @return Training
     */
    public function setCreated($created){
        $this->created = $created;
        return $this;
    }

    /**
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
     * @return \AppBundle\Entity\User
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User\User $user
     * @return Invite
     */
    public function setUser(\AppBundle\Entity\User\User $user = null){
        $this->user = $user;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Training $sport
     * @return Invite
     */
    public function setSport(\AppBundle\Entity\Training $sport = null){
        $this->sport = $sport;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Training
     */
    public function getSport(){
        return $this->sport;
    }
	

    /**
     * @param \AppBundle\Entity\Training $training
     * @return Invite
     */
    public function setTraining(\AppBundle\Entity\Training $training = null){
        $this->training = $training;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Training
     */
    public function getTraining(){
        return $this->training;
    }
}
