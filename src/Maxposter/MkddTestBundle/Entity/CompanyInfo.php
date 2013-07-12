<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Доп.инфо о юр.лице
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_company_info")
 */
class CompanyInfo
{
    /**
     * @var integer
     * @Orm\OneToOne(targetEntity="Company", inversedBy="info")
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
     * @param \Maxposter\MkddTestBundle\Entity\Company $company
     * @return CompanyInfo
     */
    public function setCompany(\Maxposter\MkddTestBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Maxposter\MkddTestBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}