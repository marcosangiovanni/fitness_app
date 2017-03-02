<?php
namespace AppBundle\Entity;
use Cron\CronBundle\Entity\CronReport as BaseCronReport;

use Doctrine\ORM\Mapping as ORM;

class CronReport extends BaseCronReport
{
    /**
     * Set id
     *
     * @param  integer  $id
     * @return CronReport
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }
	
}
