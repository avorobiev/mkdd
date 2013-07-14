<?php
namespace Maxposter\MkddBundle\SqlFilter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Filter\SQLFilter;


/**
 * TODO:
 *      Фильтр можно добавить без конфигурации в app/config.yml через
 *      \Doctrine\Orm\Configuration::addFilter('name', $filter);
 * @see http://docs.doctrine-project.org/en/latest/reference/filters.html#configuration
 *
 *
 * @package Maxposter\MkddBundle\SqlFilter
 */
class Mkdd extends SQLFilter
{
    private $filters = array();

    public function setFilterMap(array $filters = array())
    {
        $this->filters = $filters;
    }

    private function getFilters()
    {
        return $this->filters;
    }


    /**
     * Gets the SQL query part to add to a query.
     *
     * @param  \Doctrine\ORM\Mapping\ClassMetadata  $targetEntity
     * @param  string  $targetTableAlias
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (!$targetEntity->reflClass->implementsInterface('\\Maxposter\\MkddBundle\\Entity\\MkddInterface')) {
            return '';
        }
        $filters = $this->getFilters();
        //$class = $targetEntity->getName();
        //$mkddFields = $class::getMkddFields();
        $mkddFields = $targetEntity->getReflectionClass()->getMethod('getMkddFields')->invoke(null);
        $conditions = array();
        foreach ($mkddFields as $mkddField) {
            // Фильтр по FK
            if ($targetEntity->hasAssociation($mkddField)) {
                $assocMapping = $targetEntity->getAssociationMapping($mkddField);
                if (array_key_exists($assocMapping['targetEntity'], $filters) && !empty($filters[$assocMapping['targetEntity']])) {
                    $conditions[] = sprintf(
                        '%s.%s IN (\'%s\')',
                        $targetTableAlias,
                        $targetEntity->getSingleAssociationJoinColumnName($mkddField),
                        implode('\', \'', (array) $filters[$assocMapping['targetEntity']])
                    );
                }
            } elseif (($targetEntity->getSingleIdentifierColumnName() == $mkddField) && array_key_exists($targetEntity->getName(), $filters) && !empty($filters[$targetEntity->getName()])) {
                // Фильтруем по самому себе, т.е. PK
                $conditions[] = sprintf(
                    '%s.%s IN (\'%s\')',
                    $targetTableAlias,
                    $targetEntity->getSingleIdentifierColumnName(),
                    implode('\', \'', (array) $filters[$targetEntity->getName()])
                );
            }
        }

        $result = '';
        if ($conditions) {
            $result = sprintf('((%s))', implode(') AND (', $conditions));
        }

        // FIXME: проверяли что генерировали
        // var_dump($result);

        return $result;
    }
}