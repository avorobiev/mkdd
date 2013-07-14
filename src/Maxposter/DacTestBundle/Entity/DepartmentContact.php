<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Контакты отдела автосалона
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_department_contact")
 */
class DepartmentContact
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
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;


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
     * @return DepartmentContact
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
     * Set department
     *
     * @param \Maxposter\DacTestBundle\Entity\Department $department
     * @return DepartmentContact
     */
    public function setDepartment(\Maxposter\DacTestBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Maxposter\DacTestBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }
}