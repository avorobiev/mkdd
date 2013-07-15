<?php
namespace Maxposter\DacBundle\Dac;

use \Doctrine\ORM\Mapping\ClassMetadata;
use \Doctrine\DBAL\Connection;

/**
 * @package Maxposter\DacBundle\SqlFilter
 */
class SqlFilter extends \Doctrine\ORM\Query\Filter\SQLFilter
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
            $filteredFieldName = false;
            // Фильтр по FK
            if ($targetEntity->hasAssociation($dacField)) {
                $filteredFieldName = $targetEntity->getSingleAssociationJoinColumnName($dacField);
                $assocMapping = $targetEntity->getAssociationMapping($dacField);
                $dacSettingsName = $assocMapping['targetEntity'];
            } // Фильтруем по самому себе, т.е. PK
            else if (  ($targetEntity->getSingleIdentifierColumnName() == $dacField)
                    && array_key_exists($targetEntity->getName(), $filters)
            ) {
                $filteredFieldName = $targetEntity->getSingleIdentifierColumnName();
                $dacSettingsName = $targetEntity->getName();
            }

            if ((false !== $filteredFieldName) && (isset($filters[$dacSettingsName]))) {
                $conditions[] = sprintf(
                    '%s.%s IN (\'%s\')',
                    $targetTableAlias,
                    $filteredFieldName,
                    implode('\', \'', (array) $filters[$dacSettingsName])
                );
            }


        }

        $result = '';
        if ($conditions) {
            $result = sprintf('((%s))', implode(') AND (', $conditions));
        }

        return $result;
    }
}