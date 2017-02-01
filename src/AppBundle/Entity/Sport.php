<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="sport")
 * @ORM\Entity
 */
class Sport implements Translatable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Groups({"detail"})
	 */
    private $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=256)
	 * @Groups({"detail"})
	 */
    private $title;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 */
    private $picture;

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
     * @ORM\ManyToMany(targetEntity="User", mappedBy="sports")
     */
    private $users;

	/**
     * Variable to store trainings
	 * @ORM\OneToMany(targetEntity="Training", mappedBy="sport", cascade={"remove"})
     */	
    private $trainings;
    
	/**********************
	 * BASE METHODS       *
	 **********************/
    public function __construct() {
        $this->users = new ArrayCollection();
    }

	/* Recupero il nome dal modello */
	public function __toString(){
        return (string)$this->title;
    }
	
	/**********************
	 * GET METHODS        *
	 **********************/
    public function getId(){
        return $this->id;
    }

    public function getPicture(){
        return $this->picture;
    }

    public function getTitle(){
        return $this->title;
    }

	/**********************
	 * SET METHODS        *
	 **********************/
    public function setPicture($picture){
        $this->picture = $picture;
    }

    public function setTitle($title){
        $this->title = $title;
    }

	/**********************
	 * TRANS. METHODS     *
	 **********************/
    public function setTranslatableLocale($locale){
        $this->locale = $locale;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Sport
     */
    public function addUser(\AppBundle\Entity\User $users){
        $this->users[] = $users;
        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users){
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers(){
        return $this->users;
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


    /**
     * Add training
     *
     * @param \AppBundle\Entity\Training $training
     *
     * @return Sport
     */
    public function addTraining(\AppBundle\Entity\Training $training)
    {
        $this->trainings[] = $training;

        return $this;
    }

    /**
     * Remove training
     *
     * @param \AppBundle\Entity\Training $training
     */
    public function removeTraining(\AppBundle\Entity\Training $training)
    {
        $this->trainings->removeElement($training);
    }

    /**
     * Get trainings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrainings()
    {
        return $this->trainings;
    }
}
