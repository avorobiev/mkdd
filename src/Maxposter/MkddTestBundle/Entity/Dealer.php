<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Автосалон
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_dealer")
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
     * @ORM\ManyToOne(targetEntity="Business")
     */
    private $business;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Department", mappedBy="dealers")
     */
    private $departments;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Company")
     * @ORM\JoinTable(name="test_mkdd_company_dealer")
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
     * @param \Maxposter\MkddTestBundle\Entity\Business $business
     * @return Dealer
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
     * Add departments
     *
     * @param \Maxposter\MkddTestBundle\Entity\Department $departments
     * @return Dealer
     */
    public function addDepartment(\Maxposter\MkddTestBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \Maxposter\MkddTestBundle\Entity\Department $departments
     */
    public function removeDepartment(\Maxposter\MkddTestBundle\Entity\Department $departments)
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
     * @param \Maxposter\MkddTestBundle\Entity\Company $companies
     * @return Dealer
     */
    public function addCompanie(\Maxposter\MkddTestBundle\Entity\Company $companies)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove companies
     *
     * @param \Maxposter\MkddTestBundle\Entity\Company $companies
     */
    public function removeCompanie(\Maxposter\MkddTestBundle\Entity\Company $companies)
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