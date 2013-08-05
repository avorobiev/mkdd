<?php
namespace Maxposter\DacTestBundle\Tests;

use Doctrine\ORM\EntityManager;

use Maxposter\DacTestBundle\Entity\Business;
use Maxposter\DacTestBundle\Entity\Dealer;

class EntityHelper
{
    const NS = '\\Maxposter\\DacTestBundle\\Entity\\';

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


    /**
     * Создаёт объект из массива параметров
     *
     * @param  array   $props
     * @param  string  $entityName
     * @return object
     */
    private function fromArray(array $props, $entityName)
    {
        $entityName = static::NS . $entityName;
        /** @var $meta \Doctrine\ORM\Mapping\ClassMetadata */
        $meta = $this->em->getClassMetadata($entityName);
        //$ob = new $entityName();
        $ob = $meta->newInstance();

        foreach ($meta->getFieldNames() as $field) {
            if (array_key_exists($field, $props) or array_key_exists($meta->getColumnName($field), $props)) {
                // TODO: check & force argument types
                // $type = $meta->getTypeOfField($field);
                $key = array_key_exists($field, $props) ? $field : $meta->getColumnName($field);
                $meta->getReflectionProperty($field)->setValue($ob, $props[$key]);
                unset($props[$key]);
            }
        }

        // Очищаем от всего лишнего и индексируем
        $index = array();
        foreach ($props as $k => $v) {
            if (!is_object($v) && !is_null($v)) {
                unset($props[$k]);
            } elseif (is_object($v)) {
                if (!array_key_exists(get_class($v), $index)) {
                    $index[get_class($v)] = array();
                }
                $index[get_class($v)][] = $k;
            }
        }

        // если связь -к-одному и такая связь одна, то ключ в массиве props не интересует
        // если связь -ко-много и связь одна, ключ не интересует, но значением может быть
        //                                    не одна сущность а массив с несколькими
        // если несколько связей к одной сущности то работаем только по ключу props
        // если связь nullable = true то при отсутствии значения не проставляем
        // если связь -ко-много, то через рефлексию получаем коллекцию и сеттим туда
        foreach ($meta->getAssociationMappings() as $field => $mapping) {
            $propertyKey = ucfirst($field);
            // Несколько связей к одной сущности
            // например. сам-к-себе :)
            if (1 < count($meta->getAssociationsByTargetClass($mapping['targetEntity']))) {
                if (
                    array_key_exists($propertyKey, $props)
                    && (
                        ($props[$propertyKey] instanceof $mapping['targetEntity'])
                        or (is_array($props[$propertyKey]))
                    )
                ) {
                    if ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_MANY) {
                        /** @var $coll \Doctrine\Common\Collections\Collection */
                        $coll = $meta->getReflectionProperty($field)->getValue($ob);
                        if (!is_array($props[$propertyKey])) {
                            $props[$propertyKey] = array($props[$propertyKey]);
                        }
                        foreach ($props[$propertyKey] as $value) {
                            $coll->add($value);
                        }
                    } elseif ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE) {
                        $meta->getReflectionProperty($field)->setValue($ob, $props[$propertyKey]);
                    }
                } elseif (
                    ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE)
                    && (false === $mapping['joinColumns']['0']['nullable'])
                ) {
                    $meta->getReflectionProperty($field)->setValue($ob, $this->getDefault($mapping['targetEntity']));
                }
                // Добавляем любые подходящие
            } else {
                if (
                    ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_MANY)
                    && array_key_exists($mapping['targetEntity'], $index)
                ) {
                    /** @var $coll \Doctrine\Common\Collections\Collection */
                    $coll = $meta->getReflectionProperty($field)->getValue($ob);
                    foreach ($index[$mapping['targetEntity']] as $propsKey) {
                        if (!is_array($props[$propsKey])) {
                            $props[$propsKey] = array($props[$propsKey]);
                        }
                        foreach ($props[$propsKey] as $value) {
                            $coll->add($value);
                        }
                    }
                } elseif (
                    ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE)
                    && array_key_exists($mapping['targetEntity'], $index)
                ) {
                    $meta->getReflectionProperty($field)->setValue($ob, $props[$index[$mapping['targetEntity']]['0']]);
                    // Значение по умолчанию
                } elseif (
                    ($mapping['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE)
                    && (false === $mapping['joinColumns']['0']['nullable'])
                ) {
                    $meta->getReflectionProperty($field)->setValue($ob, $this->getDefault($mapping['targetEntity']));
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
     * @return \Maxposter\DacDoctrineTestBundle\Entity\Business
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
     * @return \Maxposter\DacTestBundle\Entity\Department
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