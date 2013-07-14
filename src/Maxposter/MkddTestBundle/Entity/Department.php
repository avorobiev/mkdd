<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Подразделение автосалона
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_department")
 */
class Department implements \Maxposter\MkddBundle\Entity\MkddInterface
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
     * @var Dealer
     * @ORM\ManyToOne(targetEntity="Dealer")
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
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $dealer
     * @return Department
     */
    public function setDealer(\Maxposter\MkddTestBundle\Entity\Dealer $dealer = null)
    {
        $this->dealer = $dealer;

        return $this;
    }

    /**
     * Get dealer
     *
     * @return \Maxposter\MkddTestBundle\Entity\Dealer
     */
    public function getDealer()
    {
        return $this->dealer;
    }


    public static function getMkddFields()
    {
        return array(
            'id',
            'dealer',
        );
    }
}