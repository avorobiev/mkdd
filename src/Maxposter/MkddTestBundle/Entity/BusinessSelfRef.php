<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Нечто, что наследуется от самого себя
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_business_self_ref")
 */
class BusinessSelfRef
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
     * @ORM\OneToOne(targetEntity="BusinessSelfRef")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

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
     * @return BusinessSelfRef
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
     * @return BusinessSelfRef
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
     * Set parent
     *
     * @param \Maxposter\MkddTestBundle\Entity\BusinessSelfRef $parent
     * @return BusinessSelfRef
     */
    public function setParent(\Maxposter\MkddTestBundle\Entity\BusinessSelfRef $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Maxposter\MkddTestBundle\Entity\BusinessSelfRef
     */
    public function getParent()
    {
        return $this->parent;
    }
}