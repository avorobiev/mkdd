<?php
namespace Maxposter\MkddTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Деньго-переводы
 * связаны с автосалоном и юр.лицом
 *
 * @ORM\Entity()
 * @ORM\Table(name="test_mkdd_dealer_company_transaction")
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
     * @param \Maxposter\MkddTestBundle\Entity\Dealer $dealer
     * @return DealerCompanyTransaction
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

    /**
     * Set company
     *
     * @param \Maxposter\MkddTestBundle\Entity\Company $company
     * @return DealerCompanyTransaction
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