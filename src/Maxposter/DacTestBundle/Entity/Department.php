<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maxposter\DacBundle\Annotations\Mapping as DAC;

/**
 * Подразделение автосалона
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_department")
 */
class Department
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @DAC\Filter
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="alternative_name", type="string", length=50, nullable=true)
     */
    private $alternativeName;

    /**
     * @var Dealer
     * @ORM\ManyToOne(targetEntity="Dealer", inversedBy="departments")
     * @ORM\JoinColumn(name="dealer_dac", referencedColumnName="id")
     * @DAC\Filter(targetEntity="Dealer")
     */
    private $dealer;

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
     * @return Department
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

    /**
     * Set dealer
     *
     * @param \Maxposter\DacTestBundle\Entity\Dealer $dealer
     * @return Department
     */
    public function setDealer(\Maxposter\DacTestBundle\Entity\Dealer $dealer = null)
    {
        $this->dealer = $dealer;

        return $this;
    }

    /**
     * Get dealer
     *
     * @return \Maxposter\DacTestBundle\Entity\Dealer
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * @param string $alternativeName
     */
    public function setAlternativeName($alternativeName)
    {
        $this->alternativeName = $alternativeName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternativeName()
    {
        return $this->alternativeName;
    }
}