<?php
namespace Vivo\Indexer;

use Vivo\Metadata\MetadataManager;
use Vivo\Storage\PathBuilder\PathBuilder;
use Vivo\CMS\Model\Entity;
use Vivo\Indexer\IndexerInterface;

/**
 * FieldHelper
 */
class FieldHelper implements FieldHelperInterface
{
    /**
     * Indexer configurations for individual properties
     * array(
     *      'entity_class_name' => array(
     *          'propertyName'  => array(
     *              'enabled'   => true|false,              //Shall this property be included in index?
     *              'name'      => 'field_name_in_index',   //Field name in index
     *              'type'      => 'field_type',            //Indexer field type
     *              'indexed'   => true|false,
     *              'stored'    => true|false,
     *              'tokenized' => true|false,
     *              'multi'     => true|false,              //Is this property multi-valued?
     *          ),
     *      ),
     * )
     * @var array
     */
    protected $indexerConfigs         = array();

    /**
     * Metadata manager
     * @var MetadataManager
     */
    protected $metadataManager;

    /**
     * Path builder
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * Default options used when metadata does not specify them
     * The property metadata is merged with these defaults
     * @var array
     */
    protected $defaultIndexingOptions   = array(
        'enabled'       => false,
        'type'          => IndexerInterface::FIELD_TYPE_STRING,
        'indexed'       => true,
        'stored'        => true,
        'tokenized'     => false,
        'multi_value'   => false,
    );

    /**
     * Constructor
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     * @param \Vivo\Storage\PathBuilder\PathBuilder $pathBuilder
     */
    public function __construct(MetadataManager $metadataManager, PathBuilder $pathBuilder)
    {
        $this->metadataManager  = $metadataManager;
        $this->pathBuilder      = $pathBuilder;
    }

    /**
     * Returns indexer configuration for the specified property or for the whole entity class
     * @param string $entityClass
     * @param string|null $property
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    public function getIndexerConfig($entityClass, $property = null)
    {
        $this->loadIndexerConfigs($entityClass);
        $indexerConfigs = $this->indexerConfigs[$entityClass];
        if (is_null($property)) {
            return $indexerConfigs;
        }
        if (!isset($indexerConfigs[$property])) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Property '%s' not defined for entity '%s'", __METHOD__, $property, $entityClass));
        }
        return $indexerConfigs[$property];
    }

    /**
     * Returns indexer config for a full property name (\ClassName\property)
     * @param string $fullPropertyName
     * @return array
     */
    public function getIndexerConfigForFullPropertyName($fullPropertyName)
    {
        $parts      = explode('\\', $fullPropertyName);
        $property   = array_pop($parts);
        $class      = implode('\\', $parts);
        $indexerConfig  = $this->getIndexerConfig($class, $property);
        return $indexerConfig;
    }

    /**
     * Returns if the specified property is enabled for indexing
     * @param string $entityClass
     * @param string $property
     * @return boolean
     */
    public function isEnabled($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['enabled'];
    }

    /**
     * Returns indexer field name for the specified property
     * @param string $entityClass
     * @param string $property
     * @return string
     */
    public function getName($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['name'];
    }

    /**
     * Returns indexer field type for the specified property
     * @param string $entityClass
     * @param string $property
     * @return mixed
     */
    public function getType($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['type'];
    }

    /**
     * Returns true when the specified property is indexed
     * @param string $entityClass
     * @param string $property
     * @return bool
     */
    public function isIndexed($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['indexed'];
    }

    /**
     * Returns if the specified property is stored in the index
     * @param string $entityClass
     * @param string $property
     * @return bool
     */
    public function isStored($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['stored'];
    }

    /**
     * Returns if the specified property is tokenized
     * @param string $entityClass
     * @param string $property
     * @return boolean
     */
    public function isTokenized($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['tokenized'];
    }

    /**
     * Returns if the specified property is a multi-value
     * @param string $entityClass
     * @param string $property
     * @return boolean
     */
    public function isMultiValue($entityClass, $property)
    {
        $config = $this->getIndexerConfig($entityClass, $property);
        return $config['multi'];
    }

    /**
     * Returns full property name derived from entity and the bare property name
     * @param string $entityClass
     * @param string $property
     * @return string
     */
    public function getFullPropertyName($entityClass, $property)
    {
        $fullPropName   = '\\' . $entityClass . '\\' . $property;
        return $fullPropName;
    }

    /**
     * Looks up indexer configurations in entity metadata and adds it to property definition array
     * @param string $entityClass
     * @return void
     */
    protected function loadIndexerConfigs($entityClass)
    {
        if (!array_key_exists($entityClass, $this->indexerConfigs)) {
            $entityMetadata = $this->metadataManager->getMetadata($entityClass);
            foreach ($entityMetadata as $propertyName => $metadata) {
                $fullPropName           = $this->getFullPropertyName($entityClass, $propertyName);
                $indexerConfig          = $this->defaultIndexingOptions;
                //Indexer field name is set to the full property name by default
                $indexerConfig['name']  = $fullPropName;
                if (isset($metadata['index'])) {
                    $indexerConfig  = array_merge($indexerConfig, $metadata['index']);
                }
                $this->indexerConfigs[$entityClass][$propertyName] = $indexerConfig;
            }
        }
    }
}
