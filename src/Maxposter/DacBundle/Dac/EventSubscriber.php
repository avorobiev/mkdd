<?php
namespace Maxposter\DacBundle\Dac;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\EventSubscriber as EventSubscriberInterface;
use Doctrine\ORM\Events;
/* use Doctrine\Common\Persistence\Event\LifecycleEventArgs; */
/* use Doctrine\ORM\Event\LifecycleEventArgs; */
/* use Doctrine\ORM\Event\PreFlushEventArgs; */
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Maxposter\DacBundle\Annotations\Mapping\Service\Annotations;

class EventSubscriber implements EventSubscriberInterface
{
    /** @var \Maxposter\DacBundle\Annotations\Mapping\Service\Annotations */
    private $annotations;
    /** @var \Maxposter\DacBundle\Dac\Settings */
    private $settings;


    /**
     * Конструктор
     *
     * @param  \Maxposter\DacBundle\Annotations\Mapping\Service\Annotations  $annotations
     */
    public function __construct(Annotations $annotations)
    {
        $this->annotations = $annotations;
    }


    /**
     * Настройки
     *
     * @param  \Maxposter\DacBundle\Dac\Settings  $settings
     */
    public function setDacSettings(Settings $settings)
    {
        $this->settings = $settings;
    }


    /**
     * Настройки
     *
     * @return \Maxposter\DacBundle\Dac\Settings
     */
    private function getDacSettings()
    {
        return $this->settings;
    }


    /**
     * Аннотации
     *
     * @return \Maxposter\DacBundle\Annotations\Mapping\Service\Annotations
     */
    private function getAnnotations()
    {
        return $this->annotations;
    }


    /**
     * Проверить доступность значения
     *
     * @param  string   $dacSettingsName
     * @param  integer  $value
     * @return bool
     */
    private function isValid($dacSettingsName, $value)
    {
        $dacSettingsValue = $this->getDacSettings()->get($dacSettingsName);

        if (is_array($value)) {
            return ($dacSettingsValue && $value && !array_diff($value, $dacSettingsValue));
        } else {
            return ($dacSettingsValue && in_array($value, $dacSettingsValue));
        }
    }


    /**
     * Получить единственное значение, если возможно
     *
     * @param  string $dacSettingsName
     * @return integer|null
     */
    private function getSingleValueFor($dacSettingsName)
    {
        $dacSettingsValue = $this->getDacSettings()->get($dacSettingsName);
        if ($dacSettingsValue && (1 === count($dacSettingsValue))) {
            $value = array_shift($dacSettingsValue);

            return $value;
        }

        return null;
    }


    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
        );
    }


    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        // INSERT
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $className = get_class($entity);
            $annotations = $this->getAnnotations();
            if (!$annotations->hasDacFields($className)) {
                continue;
            }

            $recompute = false;
            $classMetadata = $em->getClassMetadata($className);
            $dacFields = $annotations->getDacFields($className);
            foreach ($dacFields as $filteredFieldName => $dacSettingsName) {
                // Пропускаем PrimaryKey
                if ($filteredFieldName == $classMetadata->getSingleIdentifierColumnName()) {
                    continue;
                }

                // У энтити НЕ указано значение
                if (null === $classMetadata->getReflectionProperty($filteredFieldName)->getValue($entity)) {
                    // Идентификатор
                    $value = $this->getSingleValueFor($dacSettingsName);
                    if (null === $value) {
                        throw new Exception(sprintf('Невозможно получить единственно верное значение для поля %s в %s', $filteredFieldName, $className));
                    }
                    // Свойство сущности - объект
                    // FIXME: связи 3х типов
                    // FIXME: тесты для связей разных типов на отдельных сущностях
                    var_dump($classMetadata->getAssociationMapping($filteredFieldName));
                    if ($classMetadata->hasAssociation($filteredFieldName)) {
                        $assocMapping = $classMetadata->getAssociationMapping($filteredFieldName);
                        if (ClassMetadata::TO_MANY & $assocMapping['type']) {
                            throw new Exception('Так быть не может');
                        } else {
                            $value = $em->getReference($classMetadata->getAssociationTargetClass($filteredFieldName), $value);
                        }
                    }
                    $classMetadata->getReflectionProperty($filteredFieldName)->setValue($entity, $value);
                    $recompute = true;
                // Указано значение, проверяем правильность
                } else {
                    $value = $classMetadata->getReflectionProperty($filteredFieldName)->getValue($entity);
                    if ($classMetadata->hasAssociation($filteredFieldName)) {
                        $assocMapping = $classMetadata->getAssociationMapping($filteredFieldName);
                        if (ClassMetadata::ONE_TO_MANY === $assocMapping['type']) {
                            // FIXME: тест
                            throw new Exception(sprintf('Связь один-ко-много не может обрабатываться, %s в %s', $filteredFieldName, $className));
                        }
                        // Для много-ко-много
                        if (ClassMetadata::MANY_TO_MANY === $assocMapping['type']) {
                            // Пример: ManyToOne всегда owningSide
                            /** @see http://docs.doctrine-project.org/en/latest/reference/unitofwork-associations.html */
                            if ($assocMapping['isOwningSide']) {
                                $assocMetadata = $em->getClassMetadata($classMetadata->getAssociationTargetClass($filteredFieldName));
                                $columnName = $assocMapping['joinTable']['inverseJoinColumns']['0']['referencedColumnName'];
                                $reflectionProp = $assocMetadata->getReflectionProperty($assocMetadata->getFieldName($columnName));
                                $values = array();
                                foreach ($value as $assocEntity) {
                                    if (UnitOfWork::STATE_NEW === $uow->getEntityState($assocEntity, UnitOfWork::STATE_NEW)) {
                                        throw new Exception(sprintf('Родительские сущности должны быть сохранены, %s в %s', $filteredFieldName, $className));
                                    }
                                    $values[] = $reflectionProp->getValue($assocEntity);
                                }
                                $value = $values;
                            } else {
                                // FIXME: test
                                throw new Exception(sprintf('Связь много-ко-много может обрабатываться только с зависимой стороны, %s в %s', $filteredFieldName, $className));
                            }
                        // Для *-к-одному получаем объект
                        } else {
                            // Для проверки нужно получить идентификатор
                            // $value = $em->getReference($classMetadata->getAssociationTargetClass($filteredFieldName), $value);
                        }
                    }
                    if (!$this->isValid($dacSettingsName, $value)) {
                        throw new Exception(sprintf('Неверное значение поля %s в %s', $filteredFieldName, $className));
                    }
                }
            }

            if ($recompute) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            //var_dump(get_class($entity));
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            //var_dump(get_class($entity));
        }

        foreach ($uow->getScheduledCollectionDeletions() as $col) {
            // FIXME: надо понять как это (коллекции) работает
            //var_dump(get_class($col));
        }

        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            //var_dump(get_class($col));
        }
    }
}