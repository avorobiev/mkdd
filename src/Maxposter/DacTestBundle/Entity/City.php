<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Город
 * Не обрабатывается dac
 *
 * @package Maxposter\DacTestBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_city")
 */
class City
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}