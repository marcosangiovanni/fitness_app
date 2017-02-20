<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\MaxDepth;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="sport")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Sport implements Translatable
{
	
	/**
	 * @Gedmo\SortablePosition
	 * @ORM\Column(name="position", type="integer")
	 */
	private $position;

	/**
     * @Vich\UploadableField(mapping="training_image", fileNameProperty="placeholder")
     * @var File
     */
    private $placeholderFile;
	
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
     * @Gedmo\Translatable
     * @ORM\Column(length=256)
	 * @Groups({"detail"})
	 * @Type("string")
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
	 * @Type("string")
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Groups({"detail"})
	 * @Type("string")
     */
    private $placeholder;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User\User", mappedBy="sports")
	 * @Type("ArrayCollection")
	 */
    private $users;

	/**
     * Variable to store trainings
	 * @ORM\OneToMany(targetEntity="Training", mappedBy="sport")
     */	
    private $trainings;
    
	/**********************
	 * BASE METHODS       *
	 **********************/
    public function __construct() {
        $this->users = new ArrayCollection();
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

    public function getPlaceholder(){
        return $this->placeholder;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getPosition(){
        return $this->position;
    }
	
	/**********************
	 * SET METHODS        *
	 **********************/
	 
	/* generic setter method*/
	public function __toString(){
        return $this->title;
    }
	 
	public function __set($property, $value){
        $this->$property = $value;
        return $this;
    }
	 
	public function setPicture($picture){
        $this->picture = $picture;
		return $this;
    }

	public function setPlaceholder($placeholder){
        $this->placeholder = $placeholder;
		return $this;
    }

    public function setTitle($title){
        $this->title = $title;
		return $this;
    }

	public function setPosition($position){
        $this->position = $position;
        return $this;
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
     * @param \AppBundle\Entity\User\User $users
     * @return Sport
     */
    public function addUser(\AppBundle\Entity\User\User $user){
    	if (!$this->users->contains($user)) {
			$this->users->add($user);
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
	
	public function setPlaceholderFile(File $placeholder = null){
        $this->placeholderFile = $placeholder;
        if ($placeholder) {
            $this->updated = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getPlaceholderFile(){
        return $this->placeholderFile;
    }
	
}
