<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata\Source;

use Zend\Db\Metadata\MetadataInterface,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Metadata\Object;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
class MysqlMetadata extends AbstractSource
{
    protected function fetchTableNameData($schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'TABLE_NAME',
            'TABLE_TYPE',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifier($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierChain(array('INFORMATION_SCHEMA', 'TABLES'))
             . ' WHERE ' . $platform->quoteIdentifier('TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifier('TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifier('TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $tables = array();
        foreach ($results->toArray() as $row) {
            $tables[$row['TABLE_NAME']] = $row['TABLE_TYPE'];
        }

        return $tables;
    }

    protected function fetchColumnData($table, $schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'C.ORDINAL_POSITION',
            'C.COLUMN_DEFAULT',
            'C.IS_NULLABLE',
            'C.DATA_TYPE',
            'C.CHARACTER_MAXIMUM_LENGTH',
            'C.CHARACTER_OCTET_LENGTH',
            'C.NUMERIC_PRECISION',
            'C.NUMERIC_SCALE',
            'C.COLUMN_NAME',
            'C.COLUMN_TYPE',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')
             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.COLUMNS C')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('C.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('C.TABLE_NAME')
             . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')'
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteValue($table);

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        $data = array();
        foreach ($results->toArray() as $row) {
            $data[$row['COLUMN_NAME']] = array(
                'ordinal_position'          => $row['ORDINAL_POSITION'],
                'column_default'            => $row['COLUMN_DEFAULT'],
                'is_nullable'               => ('YES' == $row['IS_NULLABLE']),
                'data_type'                 => $row['DATA_TYPE'],
                'character_maximum_length'  => $row['CHARACTER_MAXIMUM_LENGTH'],
                'character_octet_length'    => $row['CHARACTER_OCTET_LENGTH'],
                'numeric_precision'         => $row['NUMERIC_PRECISION'],
                'numeric_scale'             => $row['NUMERIC_SCALE'],
                'numeric_unsigned'          => (false !== strpos($row['COLUMN_TYPE'], 'unsigned')),
                'erratas'                   => array(),
            );
        }

        return $data;
    }

    protected function fetchConstraintDataNames($schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'TC.TABLE_NAME',
            'TC.CONSTRAINT_NAME',
            'TC.CONSTRAINT_TYPE',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')
             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLE_CONSTRAINTS TC')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('TC.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('TC.TABLE_NAME')
             . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        return $data;
    }

    protected function fetchConstraintDataKeys($schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'T.TABLE_NAME',
            'KCU.CONSTRAINT_NAME',
            'KCU.COLUMN_NAME',
            'KCU.ORDINAL_POSITION',
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . ' FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')

             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_NAME')

             . ' WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        return $data;
    }

    protected function fetchConstraintReferences($schema)
    {
        $platform = $this->adapter->getPlatform();

        $isColumns = array(
            'RC.TABLE_NAME',
            'RC.CONSTRAINT_NAME',
            'RC.UPDATE_RULE',
            'RC.DELETE_RULE',
            'KCU.REFERENCED_TABLE_SCHEMA',
            'KCU.REFERENCED_TABLE_NAME',
            'KCU.REFERENCED_COLUMN_NAME'
        );

        array_walk($isColumns, function (&$c) use ($platform) { $c = $platform->quoteIdentifierInFragment($c); });

        $sql = 'SELECT ' . implode(', ', $isColumns)
             . 'FROM ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.TABLES T')

             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS RC')
             . ' ON ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('RC.TABLE_NAME')

             . ' INNER JOIN ' . $platform->quoteIdentifierInFragment('INFORMATION_SCHEMA.KEY_COLUMN_USAGE KCU')
             . ' ON ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_SCHEMA')
             . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_SCHEMA')
             . ' AND ' . $platform->quoteIdentifierInFragment('RC.TABLE_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('KCU.TABLE_NAME')
             . ' AND ' . $platform->quoteIdentifierInFragment('RC.CONSTRAINT_NAME')
             . '  = ' . $platform->quoteIdentifierInFragment('KCU.CONSTRAINT_NAME')

             . 'WHERE ' . $platform->quoteIdentifierInFragment('T.TABLE_TYPE')
             . ' IN (' . $platform->quoteValueList(array('BASE TABLE', 'VIEW')) . ')';

        if ($schema != self::DEFAULT_SCHEMA) {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' = ' . $platform->quoteValue($schema);
        } else {
            $sql .= ' AND ' . $platform->quoteIdentifierInFragment('T.TABLE_SCHEMA')
                  . ' != ' . $platform->quoteValue('INFORMATION_SCHEMA');
        }

        $results = $this->adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);

        $data = array();
        foreach ($results->toArray() as $row) {
            $data[] = array_change_key_case($row, CASE_LOWER);
        }

        return $data;
    }
}
