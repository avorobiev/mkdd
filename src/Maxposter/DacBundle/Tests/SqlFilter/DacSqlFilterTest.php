<?php
namespace Maxposter\DacBundle\Tests\SqlFilter;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Symfony\Bundle\FrameworkBundle\Client;
use \Doctrine\Orm\EntityManager;
use \Maxposter\DacBundle\Dac\Dac;

class DacSqlFilterTest extends WebTestCase
{
    private
        /** @var \Symfony\Bundle\FrameworkBundle\Client */
        $client,

        /** @var \Doctrine\Orm\EntityManager */
        $em,

        /** @var \Maxposter\DacBundle\Dac\Dac */
        $dac
    ;

    public function setUp()
    {
        $this->client = static::createClient($options = array(), $serverArgs = array());

        $doctrine = $this->client->getContainer()->get('doctrine');
        $this->em = $doctrine->getManager();
        $this->dac = new \Maxposter\DacBundle\Dac\Dac($doctrine);
    }

    public function testFilterEnabledAndDisable()
    {
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filters = $this->em->getFilters();

        $this->assertArrayNotHasKey('dac_sql_filter', $filters->getEnabledFilters(), 'Изначально фильтр объявлен но не включен');

        $this->dac->enable();
        $this->assertArrayHasKey('dac_sql_filter', $filters->getEnabledFilters(), 'При включении DAC фильтр активизируется');

        $this->dac->disable();
        $this->assertArrayNotHasKey('dac_sql_filter', $filters->getEnabledFilters(), 'При отключении DAC фильтр деактивируется');
    }


    public function testAddFilterConstraint()
    {
        $this->dac->enable();

        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $this->em->getFilters()->getFilter('dac_sql_filter');

        $dacEntity = $this->getMock('\\Maxposter\\DacBundle\\Entity\\DacInterface');
        $dacEntity::staticExpects($this->atLeastOnce())
            ->method('getDacFields')
            ->will($this->returnValue(array('id', 'foreign_id')))
        ;
        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($dacEntity), $this->em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';

        $filter->setFilterMap(array(
            get_class($dacEntity) => array(24,36),
        ));

        $result = $filter->addFilterConstraint($meta, 'a_');
        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $result);
    }

}