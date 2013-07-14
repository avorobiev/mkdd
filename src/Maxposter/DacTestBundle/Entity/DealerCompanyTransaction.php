<?php
namespace Maxposter\DacTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Деньго-переводы
 * связаны с автосалоном и юр.лицом
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_dac_dealer_company_transaction")
 */
class DealerCompanyTransaction
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
     * @var Company
     * @ORM\ManyToOne(targetEntity="Company")
     */
    private $company;

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
     * @return DealerCompanyTransaction
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
     * @return DealerCompanyTransaction
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

    /**
     * Set company
     *
     * @param \Maxposter\DacTestBundle\Entity\Company $company
     * @return DealerCompanyTransaction
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