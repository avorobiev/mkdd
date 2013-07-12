<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Юр.лицо
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_company")
 */
class Company
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
     * @var Business
     * @ORM\ManyToOne(targetEntity="Business")
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
     * @param \Maxposter\MkddTestBundle\Entity\Business $business
     * @return Company
     */
    public function setBusiness(\Maxposter\MkddTestBundle\Entity\Business $business = null)
    {
        $this->business = $business;

        return $this;
    }

    /**
     * Get business
     *
     * @return \Maxposter\MkddTestBundle\Entity\Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * Add dealers
     *
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $dealers
     * @return Company
     */
    public function addDealer(\Maxposter\MkddTestBundle\Entity\Dealer $dealers)
    {
        $this->dealers[] = $dealers;

        return $this;
    }

    /**
     * Remove dealers
     *
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $dealers
     */
    public function removeDealer(\Maxposter\MkddTestBundle\Entity\Dealer $dealers)
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
     * @param \Maxposter\MkddTestBundle\Entity\CompanyInfo $info
     * @return Company
     */
    public function setInfo(\Maxposter\MkddTestBundle\Entity\CompanyInfo $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \Maxposter\MkddTestBundle\Entity\CompanyInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}