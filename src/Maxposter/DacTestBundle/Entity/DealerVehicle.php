<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Машинка автосалона
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_dealer_vehicle")
 */
class DealerVehicle
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
     * @return DealerVehicle
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
     * @return DealerVehicle
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
}