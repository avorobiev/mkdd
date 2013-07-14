<?php
namespace Maxposter\MkddBundle\Tests\SqlFilter;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MkddTest extends WebTestCase
{
    public function testFilterEnabled()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        /** @var $em \Doctrine\Orm\EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filters = $em->getFilters();
        $this->assertArrayHasKey('mkdd_filter', $filters->getEnabledFilters());
    }


    public function testAddFilterConstraint()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        /** @var $em \Doctrine\Orm\EntityManager */
        $em = $client->getContainer()->get('doctrine')->getManager();
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $em->getFilters()->getFilter('mkdd_filter');

        $mkddEntity = $this->getMock('\\Maxposter\\MkddBundle\\Entity\\MkddInterface');
        $mkddEntity::staticExpects($this->atLeastOnce())
            ->method('getMkddFields')
            ->will($this->returnValue(array('id', 'foreign_id')))
        ;
        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($mkddEntity), $em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';

        $filter->setFilterMap(array(
            get_class($mkddEntity) => array(24,36),
        ));

        $result = $filter->addFilterConstraint($meta, 'a_');
        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $result);
    }

}