<?php
namespace Maxposter\DacBundle\Dac;

use \Doctrine\ORM\Mapping\ClassMetadata;
use \Doctrine\DBAL\Connection;
use Maxposter\DacBundle\Annotations\Mapping\Service\Annotations;
use Maxposter\DacBundle\Exceptions\DacMappingException;

/**
 * @package Maxposter\DacBundle\SqlFilter
 */
class SqlFilter extends \Doctrine\ORM\Query\Filter\SQLFilter
{

    private
        /** @var Settings */
        $dacSettings,

        /** @var \Maxposter\DacBundle\Annotations\Mapping\Service\Annotations */
        $annotations,

        /** @var \Doctrine\ORM\Mapping\ClassMetadata */
        $targetEntity;

    public function setAnnotations(Annotations $annotations)
    {
        $this->annotations = $annotations;
    }

    public function setDacSettings(Settings $dacSettings)
    {
        $this->dacSettings = $dacSettings;
    }

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param  \Doctrine\ORM\Mapping\ClassMetadata $targetEntity
     * @param  string $targetTableAlias
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $entityName = $targetEntity->getReflectionClass()->getName();
        $this->targetEntity = $targetEntity;

        if (!$this->annotations->hasDacFields($entityName)) {
            return '';
        }

        $dacFields = $this->annotations->getDacFields($entityName);
        $conditions = array();
        foreach ($dacFields as $filteredFieldName => $dacSettingsName) {
            $filteredColumnName = $this->getColumnNameByField($filteredFieldName);
            $dacSettings= $this->getDacSettings()->get($dacSettingsName);
            if ($dacSettings)
            {
                $conditions[] = sprintf(
                    '%s.%s IN (\'%s\')',
                    $targetTableAlias,
                    $filteredColumnName,
                    implode('\', \'', (array)$dacSettings)
                );
            }
        }

        if ($conditions) {
            $result = sprintf('((%s))', implode(') OR (', $conditions));
        } else {
            $result = sprintf('%s', '1=2');
        }

        return $result;
    }

    private function getDacSettings()
    {
        if (is_null($this->dacSettings)) {
            throw new Exception('Ошибка в инициализации SQL-фильтра: не заданы параметры фильтрации');
        }

        return $this->dacSettings;
    }

    private function getColumnNameByField($fieldName)
    {
        if (in_array($fieldName, $this->targetEntity->getFieldNames())) {
            $columnNames = $this->targetEntity->columnNames;
            $columnName = $columnNames[$fieldName];
        } elseif (in_array($fieldName, $this->targetEntity->getAssociationNames())) {
            $associationMappings = $this->targetEntity->associationMappings;

            if (count($associationMappings[$fieldName]['joinColumnFieldNames']) !== 1) {
                throw new DacMappingException();
            }
            $columnName = array_pop($associationMappings[$fieldName]['joinColumnFieldNames']);
        } else {
            throw new DacMappingException("ClassMetadata не содержит информацию о названии столбца в таблице для свойства " . $fieldName);
        }

        return $columnName;
    }
}