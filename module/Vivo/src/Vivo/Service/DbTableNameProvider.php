<?php
namespace Vivo\Service;

/**
 * DbTableNameProvider
 * Provides core db table names
 */
class DbTableNameProvider
{
    /**
     * Table names
     * @var array
     */
    protected $tableNames   = array();

    /**
     * Constructor
     * @param array $tableNames
     */
    public function __construct(array $tableNames)
    {
        $this->tableNames   = $tableNames;
    }

    /**
     * Returns actual name of a core db table
     * @param string $symbolicTableName
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function getTableName($symbolicTableName)
    {
        if (!array_key_exists($symbolicTableName, $this->tableNames)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Symbolic table name '%s' not recognized", __METHOD__, $symbolicTableName));
        }
        $tableName  = $this->tableNames[$symbolicTableName];
        return $tableName;
    }

    /**
     * Returns all core table names as symbolic_name => real_name
     * @return array
     */
    public function getTableNames()
    {
        return $this->tableNames;
    }
}
