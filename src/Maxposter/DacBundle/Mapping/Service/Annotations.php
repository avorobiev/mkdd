<?php
namespace Maxposter\DacBundle\Mapping\Service;

use Doctrine\ORM\EntityManager;
use Maxposter\DacBundle\Mapping\Driver\Annotations as Driver;

class Annotations
{
    /** @var \Doctrine\ORM\EntityManager  */
    private $em;
    /** @var \Maxposter\DacBundle\Mapping\Driver\Annotations  */
    private $driver;

    /** @var array */
    private $map;

    /**
     * @param EntityManager $em
     * @param Driver $driver
     */
    public function __construct(EntityManager $em, Driver $driver)
    {
        $this->em     = $em;
        $this->driver = $driver;
    }


    /**
     * @return array
     */
    private function load()
    {
        if (!$this->map) {
            $this->map = array();
            foreach ($this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames() as $className) {
                $this->map[$className] = $this->driver->getAnnotatedColumns($className);
            }
        }

        return $this->map;
    }


    /**
     * @param $className
     * @return bool
     */
    public function hasDacFields($className)
    {
        $this->load();

        return array_key_exists($className, $this->map) && $this->map[$className];
    }


    /**
     * @param $className
     * @return array
     */
    public function getDacFields($className)
    {
        $this->load();

        return $this->hasDacFields($className) ? $this->map[$className] : array();
    }
}