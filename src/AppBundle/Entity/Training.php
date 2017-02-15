<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

use CrEOF\Spatial\PHP\Types\Geometry\Point;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;
/**
 * @ORM\Table(name="training")
 * @ORM\Table(indexes={@ORM\Index(name="idx_training_position", columns={"position"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrainingRepository")
 * @Vich\Uploadable
 */
class Training
{
	/**
     * @Vich\UploadableField(mapping="training_image", fileNameProperty="picture")
     * @var File
     */
    private $imageFile;
	
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
    private $sport_id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=256)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $title;

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
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 * @Type("DateTime<'Y-m-d\TH:i:sO'>")
	 */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 * @Type("DateTime<'Y-m-d\TH:i:sO'>")
	 */
    private $end;

    /**
     * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({"detail"})
	 * @Type("DateTime<'Y-m-d\TH:i:sO'>")
	 */
    private $cutoff;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : true})
	 */
    private $is_public;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default" : 0})
	 * @Groups({"detail"})
	 * @Type("float")
	 */
    private $price;
	
    /**
	 * @ORM\Column(type="point")
	 * @Groups({"detail"})
	 * @Accessor(getter="getPositionApi", setter="setPositionApi")
	 * @Type("array")
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
	 * @Type("AppBundle\Entity\Sport")
     */
    private $sport;
	
	/**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User\User", inversedBy="trainings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @MaxDepth(2)
	 * @Groups({"detail"})
	 * @SerializedName("creator")
	 * @Type("AppBundle\Entity\User\User")
	 */
    private $user;

	/**
     * @ORM\OneToMany(targetEntity="Subscribed", mappedBy="training", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @MaxDepth(3)
	 * @Groups({"detail"})
	 * @Type("AppBundle\Entity\Subscribed")
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
     * @param \AppBundle\Entity\User\User $user
     * @return Training
     */
    public function setUser(\AppBundle\Entity\User\User $user = null){
        $this->user = $user;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\User\User
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


	/*************************
	 * SUBSCRIBED MANAGEMENT *
	 *************************/
	
	/**
     * @param \AppBundle\Entity\Subscribed $subscribed
     * @return Training
     */
    public function setSubscribed(\AppBundle\Entity\Subscribed $subscribed){
		foreach ($subscribed as $s) {
            $this->addSubscribed($s);
        }
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Subscribed $subscribed
     * @return Training
     */
    public function addSubscribed(\AppBundle\Entity\Subscribed $subscribed){
    	$subscribed->setTraining($this);
        $this->subscribed[] = $subscribed;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Subscribed $subscribed
     */
    public function removeSubscribed(\AppBundle\Entity\Subscribed $subscribed){
    	foreach ($this->subscribed as $k => $s) {
            if ($s->getId() == $succursale->getId()) {
                unset($this->succursales[$k]);
            }
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribed(){
        return $this->subscribed;
    }
	
	
	/*************************
	 * SUBSCRIBED MANAGEMENT *
	 *************************/
	/*
	 *  Doctrine only upload file if any field is modified 
	 */
	public function setImageFile(File $image = null){
        $this->imageFile = $image;
        if ($image) {
            $this->updated = new \DateTimeImmutable();
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
	 * LATLON MANAGEMENT *
     *********************/
    public function getPositionApi(){
        $position = $this->position;
		return array('lat' => $position->getX(), 'lng' => $position->getY());
    }
	
    public function setPositionApi($ar_position){
    	$this->setPosition(new Point($ar_position['lat'],$ar_position['lng']));
		return $this;
    }
	
	
	public function setLatLng($latlng){
        $this->setPosition(new Point($latlng['lat'], $latlng['lng']));
        return $this;
    }

    /**
     * @Assert\NotBlank()
     * @OhAssert\LatLng()
     */
    public function getLatLng(){
    	if($this->getPosition()){
	        return array('lat'=>$this->getPosition()->getX(),'lng'=>$this->getPosition()->getY());
    	}
    }
	
}
