<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ORM\Table(name="config")
 * @ORM\Entity
 * @ORM\Table(indexes={@ORM\Index(name="idx_code", columns={"code"})})
 */
class Config
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
     * @ORM\Column(length=256, unique=true)
	 * @Groups({"detail"})
	 * @Type("string")
     */
    private $code;

    /**
     * @ORM\Column(length=1256)
	 * @Type("string")
     */
    private $value;

    /**
     * @ORM\Column(type="text")
	 * @Type("string")
     */
    private $description;

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


	/**********************
	 * GET METHODS        *
	 **********************/
	 
	public function getId(){
        return $this->id;
    }

    public function getCode(){
        return $this->code;
    }

    public function getValue(){
        return $this->value;
    }

    public function getDescription(){
        return $this->description;
    }	

	/**********************
	 * SET METHODS        *
	 **********************/
	 
    public function setCode($code){
        $this->code = $code;
		return $this;
    }

    public function setValue($value){
        $this->value = $value;
		return $this;
    }	

    public function setDescription($description){
        $this->description = $description;
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

	/*********************
	 * EXPORT MANAGEMENT *
	 *********************/
	
	public function getValueForExport(\AppBundle\Entity\User\User $user){
    	switch ($this->getCode()) {
		    case 'facebook_refresh':
				return $user->hasToBeFacebookFriendsListRefreshed($this);
		        break;
			default:
		        return $this->getValue();
		        break;
		}
    }
	

}
