<?php
namespace Maxposter\DacBundle\SqlFilter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Filter\SQLFilter;


/**
 * @package Maxposter\DacBundle\SqlFilter
 */
class DacSqlFilter extends SQLFilter
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
        if (!$targetEntity->reflClass->implementsInterface('\\Maxposter\\DacBundle\\Entity\\DacInterface')) {
            return '';
        }
        $filters = $this->getFilters();
        //$class = $targetEntity->getName();
        //$dacFields = $class::getDacFields();
        $dacFields = $targetEntity->getReflectionClass()->getMethod('getDacFields')->invoke(null);
        $conditions = array();
        foreach ($dacFields as $dacField) {
            // Фильтр по FK
            if ($targetEntity->hasAssociation($dacField)) {
                $assocMapping = $targetEntity->getAssociationMapping($dacField);
                if (array_key_exists($assocMapping['targetEntity'], $filters) && !empty($filters[$assocMapping['targetEntity']])) {
                    $conditions[] = sprintf(
                        '%s.%s IN (\'%s\')',
                        $targetTableAlias,
                        $targetEntity->getSingleAssociationJoinColumnName($dacField),
                        implode('\', \'', (array) $filters[$assocMapping['targetEntity']])
                    );
                }
            } elseif (($targetEntity->getSingleIdentifierColumnName() == $dacField) && array_key_exists($targetEntity->getName(), $filters) && !empty($filters[$targetEntity->getName()])) {
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