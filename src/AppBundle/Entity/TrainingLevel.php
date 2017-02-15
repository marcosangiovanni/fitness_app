<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity
 * @ORM\Table(name="training_level")
 */
class TrainingLevel
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
     * @ORM\Column(length=256)
	 * @Groups({"detail"})
	 * @Type("string")
	 */
    private $title;

    /**
     * @ORM\Column
	 * @Type("text")
	 */
    private $description;

	/**
     * @ORM\ManyToOne(targetEntity="Training", inversedBy="level")
     * @ORM\JoinColumn(name="training_id", referencedColumnName="id")
	 * @Groups({"detail"})
	 * @Type("AppBundle\Entity\Training")
     */
//    private $training;

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
     * @return string 
     */
    public function getDescription(){
        return $this->description;
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
     * @return string 
     */
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
     * @param \AppBundle\Entity\Training $training
     * @return Training
     */
    public function setTraining(\AppBundle\Entity\Training $training = null){
        $this->training = $training;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Sport 
     */
    public function getTraining(){
        return $this->training;
    }

}
