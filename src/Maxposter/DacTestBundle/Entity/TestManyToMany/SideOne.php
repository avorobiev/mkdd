<?php
namespace Maxposter\DacTestBundle\Entity\TestManyToMany;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Сущность определяющая настройки мкдд
 * Inverse side
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_m2m_side_one")
 */
class SideOne
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", name="inverse_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="SideTwo", mappedBy="sideOne")
     */
    private $sideTwo;

    public function __construct()
    {
        $this->sideTwo = new ArrayCollection();
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
     * Add sideTwo
     *
     * @param \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo $sideTwo
     * @return SideOne
     */
    public function addSideTwo(\Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo $sideTwo)
    {
        $this->sideTwo[] = $sideTwo;

        return $this;
    }

    /**
     * Remove sideTwo
     *
     * @param \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo $sideTwo
     */
    public function removeSideTwo(\Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo $sideTwo)
    {
        $this->sideTwo->removeElement($sideTwo);
    }

    /**
     * Get sideTwo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSideTwo()
    {
        return $this->sideTwo;
    }
}