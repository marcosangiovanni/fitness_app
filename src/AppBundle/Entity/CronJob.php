<?php
namespace AppBundle\Entity;
use Cron\CronBundle\Entity\CronJob as BaseCronJob;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CronJobRepository")
 */
class CronJob extends BaseCronJob
{
    /**
     * Set id
     *
     * @param  integer  $id
     * @return CronJob
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }
	
}
