<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;


/**
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 * @ORM\Table(indexes={@ORM\Index(name="idx_user_id", columns={"user_id"})})
 */
class Log
{
	
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
     * @ORM\Column(type="integer", length=100)
     */
    private $user_id;

    /**
	 * @ORM\Column(type="point", nullable=true)
	 */
    private $position;

    /**
	 * @ORM\Column(type="float", nullable=true)
	 */
    private $lng;

    /**
	 * @ORM\Column(type="float", nullable=true)
	 */
    private $lat;

    /**
	 * @ORM\Column(type="float", nullable=true)
	 */
    private $distance;

    /**
	 * @ORM\Column(type="float", nullable=true)
	 */
    private $max_price;

    /**
	 * @ORM\Column(type="simple_array", nullable=true)
	 */
    private $sports;

    /**
	 * @ORM\Column(type="text", nullable=true)
	 */
    private $sports_obj;

    /**
	 * @ORM\Column(type="string", nullable=true)
	 */
    private $date;

    /**
	 * @ORM\Column(type="date", nullable=true)
	 */
    private $date_obj;

    /**
	 * @ORM\Column(type="string", nullable=true)
	 */
    private $date_op;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fullquery;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
    private $user;


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
     * @return integer
     */
    public function getUserId(){
        return $this->user_id;
    }

    /**
     * @return point
     */
    public function getPosition(){
        return $this->position;
    }

    /**
     * @return float
     */
    public function getLng(){
        return $this->lng;
    }

    /**
     * @return float
     */
    public function getLat(){
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getDistance(){
        return $this->distance;
    }

    /**
     * @return float
     */
    public function getMaxPrice(){
        return $this->max_price;
    }

    /**
     * @return array
     */
    public function getSports(){
        return $this->sports;
    }

    /**
     * @return array
     */
    public function getSportsObj(){
        return $this->sports_obj;
    }

    /**
     * @return string
     */
    public function getDate(){
        return $this->date;
    }

    /**
     * @return \Datetime
     */
    public function getDateObj(){
        return $this->date_obj;
    }

    /**
     * @return string
     */
    public function getDateOp(){
        return $this->date_op;
    }

    /**
     * @return string
     */
    public function getFullquery(){
        return $this->fullquery;
    }

	/**********************
	 * SET METHODS        *
	 **********************/

	/**
     * @param integer $userId
     * @return Log
     */
    public function setUserId($userId){
        $this->user_id = $userId;
        return $this;
    }

    /**
     * @param point $position
     * @return Log
     */
    public function setPosition($position){
        $this->position = $position;
        return $this;
    }

    /**
     * @param float $lng
     * @return Log
     */
    public function setLng($lng){
        $this->lng = $lng;
        return $this;
    }

    /**
     * @param float $lat
     * @return Log
     */
    public function setLat($lat){
        $this->lat = $lat;
        return $this;
    }

    /**
     * @param float $distance
     * @return Log
     */
    public function setDistance($distance){
        $this->distance = $distance;
        return $this;
    }

    /**
     * @param float $max_price
     * @return Log
     */
    public function setMaxPrice($max_price){
        $this->max_price = $max_price;
        return $this;
    }

    /**
     * @param array $sports
     * @return Log
     */
    public function setSports($sports){
        $this->sports = $sports;
        return $this;
    }

    /**
     * @param array $sports
     * @return Log
     */
    public function setSportsObj($sports_obj){
        $this->sports_obj = $sports_obj;
        return $this;
    }

    /**
     * @param string $date
     * @return Log
     */
    public function setDate($date){
        $this->date = $date;
        return $this;
    }

    /**
     * @param \DateTime $date
     * @return Log
     */
    public function setDateObj($date_obj){
        $this->date_obj = $date_obj;
        return $this;
    }

    /**
     * @param string $dateOp
     * @return Log
     */
    public function setDateOp($dateOp){
        $this->date_op = $dateOp;
        return $this;
    }

    /**
     * @param string $fullquery
     * @return Log
     */
    public function setFullquery($fullquery){
        $this->fullquery = $fullquery;
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
     * @return Sport
     */
    public function setCreated($created){
        $this->created = $created;
        return $this;
    }

    /**
     * @param \DateTime $updated
     * @return Sport
     */
    public function setUpdated($updated){
        $this->updated = $updated;
        return $this;
    }


	/************************
	 * RELATIONSHIP METHODS *
	 ************************/

    /**
     * @param \AppBundle\Entity\User\User $user
     * @return Log
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
	

    /*********************
	 * LATLON MANAGEMENT *
     *********************/

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
