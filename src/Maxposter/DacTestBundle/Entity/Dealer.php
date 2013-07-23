<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Автосалон
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_dealer")
 */
class Dealer
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
     * @ORM\ManyToOne(targetEntity="Business", inversedBy="dealers")
     */
    private $business;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Department", mappedBy="dealer")
     */
    private $departments;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Company")
     * @ORM\JoinTable(name="test_dac_company_dealer")
     */
    private $companies;


    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->companies   = new ArrayCollection();
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
     * @return Dealer
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
     * @return Dealer
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
     * Add departments
     *
     * @param \Maxposter\DacTestBundle\Entity\Department $departments
     * @return Dealer
     */
    public function addDepartment(\Maxposter\DacTestBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \Maxposter\DacTestBundle\Entity\Department $departments
     */
    public function removeDepartment(\Maxposter\DacTestBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Add companies
     *
     * @param \Maxposter\DacTestBundle\Entity\Company $companies
     * @return Dealer
     */
    public function addCompanie(\Maxposter\DacTestBundle\Entity\Company $companies)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove companies
     *
     * @param \Maxposter\DacTestBundle\Entity\Company $companies
     */
    public function removeCompanie(\Maxposter\DacTestBundle\Entity\Company $companies)
    {
        $this->companies->removeElement($companies);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompanies()
    {
        return $this->companies;
    }
}