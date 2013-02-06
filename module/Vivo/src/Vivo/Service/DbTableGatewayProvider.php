<?php
namespace Vivo\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;

class DbTableGatewayProvider
{
    /**
     * Registered tables
     * @var array
     */
    private $tables   = array();

    /**
     * Returns table gateway for the given symbolic name
     * @param string $symbolicTableName
     * @throws Exception\InvalidArgumentException
     * @return TableGateway
     */
    public function get($symbolicTableName)
    {
        if (!$this->has($symbolicTableName)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Symbolic table name '%s' does not exist", __METHOD__, $symbolicTableName));
        }
        if (!isset($this->tables[$symbolicTableName]['table_gateway'])) {
            $realTableName  = $this->tables[$symbolicTableName]['real_table'];
            $zdba           = $this->tables[$symbolicTableName]['zdba'];
            $this->tables[$symbolicTableName]['table_gateway']  = new TableGateway($realTableName, $zdba);
        }
        return $this->tables[$symbolicTableName]['table_gateway'];
    }

    /**
     * Registers table
     * @param string $symbolicTableName
     * @param \Zend\Db\Adapter\Adapter $zdba
     * @param string $realTableName
     * @throws Exception\InvalidArgumentException
     */
    public function add($symbolicTableName, ZendDbAdapter $zdba, $realTableName)
    {
        if ($this->has($symbolicTableName)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Symbolic table name '%s' already exists", __METHOD__, $symbolicTableName));
        }
        $data   = array(
            'zdba'          => $zdba,
            'real_table'    => $realTableName,
        );
        $this->tables[$symbolicTableName]   = $data;
    }

    /**
     * Returns true when the specified symbolic table name has been registered with the TableGatewayProvider
     * @param string $symbolicTableName
     * @return bool
     */
    public function has($symbolicTableName)
    {
        if (array_key_exists($symbolicTableName, $this->tables)) {
            return true;
        }
        return false;
    }
}