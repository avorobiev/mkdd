<?php
namespace Maxposter\DacBundle\Tests\SqlFilter;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DacTest extends WebTestCase
{
    public function testFilterEnabled()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        /** @var $em \Doctrine\Orm\EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filters = $em->getFilters();
        $this->assertArrayHasKey('dac_filter', $filters->getEnabledFilters());
    }


    public function testAddFilterConstraint()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        /** @var $em \Doctrine\Orm\EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $em->getFilters()->getFilter('dac_filter');

        $dacEntity = $this->getMock('\\Maxposter\\DacBundle\\Entity\\DacInterface');
        $dacEntity::staticExpects($this->atLeastOnce())
            ->method('getDacFields')
            ->will($this->returnValue(array('id', 'foreign_id')))
        ;
        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($dacEntity), $em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';

        $filter->setFilterMap(array(
            get_class($dacEntity) => array(24,36),
        ));

        $result = $filter->addFilterConstraint($meta, 'a_');
        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $result);
    }

}