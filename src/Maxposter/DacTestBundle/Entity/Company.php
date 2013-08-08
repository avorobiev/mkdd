<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Maxposter\DacBundle\Annotations\Mapping as DAC;

/**
 * Юр.лицо
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_company")
 */
class Company
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
     * @var Business
     * @ORM\ManyToOne(targetEntity="Business", inversedBy="companies")
     * @DAC\Filter
     */
    private $business;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Dealer")
     */
    private $dealers;

    /**
     * @var integer
     * @Orm\OneToOne(targetEntity="CompanyInfo", mappedBy="company")
     */
    private $info;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dealers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Company
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
     * Set business
     *
     * @param \Maxposter\DacTestBundle\Entity\Business $business
     * @return Company
     */
    public function setBusiness(\Maxposter\DacTestBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \Maxposter\DacTestBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Add dealers
     *
     * @param \Maxposter\DacTestBundle\Entity\Dealer $dealers
     * @return Company
     */
    public function addDealer(\Maxposter\DacTestBundle\Entity\Dealer $dealers)
    {
        $this->dealers[] = $dealers;

        return $this;
    }

    /**
     * Remove dealers
     *
     * @param \Maxposter\DacTestBundle\Entity\Dealer $dealers
     */
    public function removeDealer(\Maxposter\DacTestBundle\Entity\Dealer $dealers)
    {
        $this->dealers->removeElement($dealers);
    }

    /**
     * Get dealers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDealers()
    {
        return $this->dealers;
    }

    /**
     * Set info
     *
     * @param \Maxposter\DacTestBundle\Entity\CompanyInfo $info
     * @return Company
     */
    public function setInfo(\Maxposter\DacTestBundle\Entity\CompanyInfo $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \Maxposter\DacTestBundle\Entity\CompanyInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}