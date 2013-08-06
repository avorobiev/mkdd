<?php
namespace Maxposter\DacTestBundle\Entity\TestManyToMany;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Maxposter\DacBundle\Annotations\Mapping as DAC;

/**
 * Сущность фильтруемая по мкдд
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_m2m_side_two")
 */
class SideTwo
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="SideOne", inversedBy="sideTwo")
     * @ORM\JoinTable(
     *      name="test_dac_m2m_sides_one_two",
     *      joinColumns={@ORM\JoinColumn(name="sidetwo_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sideone_id", referencedColumnName="inverse_id")}
     * )
     * @DAC\Filter(targetEntity="SideOne")
     */
    private $sideOne;

    public function __construct()
    {
        $this->sideOne = new ArrayCollection();
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
     * Add sideOne
     *
     * @param \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne $sideOne
     * @return SideTwo
     */
    public function addSideOne(\Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne $sideOne)
    {
        $this->sideOne[] = $sideOne;
        $sideOne->addSideTwo($this);

        return $this;
    }

    /**
     * Remove sideOne
     *
     * @param \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne $sideOne
     */
    public function removeSideOne(\Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne $sideOne)
    {
        $this->sideOne->removeElement($sideOne);
    }

    /**
     * Get sideOne
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSideOne()
    {
        return $this->sideOne;
    }
}