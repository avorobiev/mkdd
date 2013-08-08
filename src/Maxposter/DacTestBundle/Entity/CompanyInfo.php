<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maxposter\DacBundle\Annotations\Mapping as DAC;

/**
 * Доп.инфо о юр.лице
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_company_info")
 */
class CompanyInfo
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\OneToOne(targetEntity="Company", inversedBy="info")
     * @DAC\Filter()
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $data;


    /**
     * Set data
     *
     * @param string $data
     * @return CompanyInfo
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set company
     *
     * @param \Maxposter\DacTestBundle\Entity\Company $company
     * @return CompanyInfo
     */
    public function setCompany(\Maxposter\DacTestBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Maxposter\DacTestBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}