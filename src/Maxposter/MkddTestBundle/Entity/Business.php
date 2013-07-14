<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Организация
 * Связана с:
 *  - салоном (один-много) Dealer
 *  - юрлицом (один-много) Company
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_business")
 */
class Business implements \Maxposter\MkddBundle\Entity\MkddInterface
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Dealer", mappedBy="business")
     */
    private $dealers;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Dealer", mappedBy="business")
     */
    private $companies;


    public function __construct()
    {
        $this->dealers = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
     * @return Business
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
     * Add dealers
     *
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $dealers
     * @return Business
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
     * Add companies
     *
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $companies
     * @return Business
     */
    public function addCompanie(\Maxposter\MkddTestBundle\Entity\Dealer $companies)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove companies
     *
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $companies
     */
    public function removeCompanie(\Maxposter\MkddTestBundle\Entity\Dealer $companies)
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