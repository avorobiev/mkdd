<?php
namespace Maxposter\MkddTestBundle\Tests;

use Doctrine\ORM\EntityManager;

use Maxposter\MkddTestBundle\Entity\Business;
use Maxposter\MkddTestBundle\Entity\Dealer;

class EntityHelper
{
    const NS = '\\Maxposter\\MkddTestBundle\\Entity\\';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     *
     */
    private $defaultObjects = array();

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    private function getUniqueCounter()
    {
        return ++$this->counter;
    }


    private function fromArray(array $props, $class)
    {
        $class = static::NS . $class;
        /** @var $meta \Doctrine\ORM\Mapping\ClassMetadata */
        $meta = $this->em->getClassMetadata($class);
        $ob = new $class();

        foreach ($meta->getFieldNames() as $field) {
            if (array_key_exists($field, $props)) {
                $method = sprintf('set%s', ucfirst($field));
                $ob->$method($props[$field]);
            }
        }

        foreach ($meta->getAssociationMappings() as $field => $mapping) {
            if (
            $coll = array_reduce(
                $props,
                function (&$res, $item) use ($mapping) {
                    if ($item instanceof $mapping['targetEntity']) {
                        if (is_null($res)) {
                            $res = array();
                        }
                        $res[] = $item;
                    }

                    return $res;
                })
            ) {
                if ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE) {
                    $method = sprintf('set%s', ucfirst($field));
                    $ob->$method($coll['0']);
                } elseif ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_MANY) {
                    $method = sprintf('add%s', ucfirst($field));
                    foreach ($coll as $relOb) {
                        $ob->$method($relOb);
                    }
                }
            }
        }

        return $ob;
    }


    private function setDefault($ob)
    {
        $arr = explode('\\', get_class($ob));
        $entityName = end($arr);
        if (!array_key_exists($entityName, $this->defaultObjects)) {
            $this->defaultObjects[$entityName] = $ob;
        }

        return $this->defaultObjects[$entityName];
    }


    private function getDefault($entityName)
    {
        if (empty($this->defaultObjects[$entityName])) {
            $method = sprintf('make%s', $entityName);
            $ob = $this->$method();
            $this->defaultObjects[$entityName] = $ob;
        }

        return $this->defaultObjects[$entityName];
    }


    /**
     * @param array $props
     * @return \Maxposter\MkddDoctrineTestBundle\Entity\Business
     */
    public function makeBusiness(array $props = array())
    {
        $props = array_merge(array(
            'name' => sprintf('business-%d', $this->getUniqueCounter()),
        ), $props);
        $ob = $this->fromArray($props, 'Business');

        $this->em->persist($ob);
        $this->em->flush();

        return $ob;
    }


    /**
     * @param array $props
     * @return Dealer
     */
    public function makeDealer(array $props = array())
    {
        $props = array_merge(array(
            'name' => sprintf('dealer-%d', $this->getUniqueCounter()),
        ), $props);

        if (empty($props['Business']) || !($props['Business'] instanceof Business)) {
            $props['Business'] = $this->getDefault('Business');
        }

        $ob = $this->fromArray($props, 'Dealer');

        $this->em->persist($ob);
        $this->em->flush();

        $this->setDefault($ob);

        return $ob;
    }


    /**
     * @param array $props
     * @return \Maxposter\MkddDoctrineTestBundle\Entity\Department
     */
    public function makeDepartment(array $props = array())
    {
        $props = array_merge(array(
            'name' => sprintf('department-%d', $this->getUniqueCounter()),
        ), $props);

        if (empty($props['Dealer']) || !($props['Dealer'] instanceof Dealer)) {
            $props['Dealer'] = $this->getDefault('Dealer');
        }

        $ob = $this->fromArray($props, 'Department');

        $this->em->persist($ob);
        $this->em->flush();

        return $ob;
    }
}