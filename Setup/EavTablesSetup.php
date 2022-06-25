<?php
/**
 * install eav tables
 */
namespace OAG\Blog\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use OAG\Blog\Setup\PostSetup;

class EavTablesSetup
{

    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    /**
     * @param SchemaSetupInterface $setup
     */
    public function __construct(
        SchemaSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    /**
     * create all eav tables
     */
    public function createEavTables($entityCode)
    {
        $this->createEAVMainTable();
        $this->createEntityTable($entityCode, 'datetime', Table::TYPE_DATETIME);
        $this->createEntityTable($entityCode, 'decimal', Table::TYPE_DECIMAL, '12,4');
        $this->createEntityTable($entityCode, 'int', Table::TYPE_INTEGER);
        $this->createEntityTable($entityCode, 'text', Table::TYPE_TEXT, '64k');
        $this->createEntityTable($entityCode, 'varchar', Table::TYPE_TEXT, 255);
    }

    /**
     * Drop all EAV tables
     *
     * @return void
     */
    public function dropEavTables()
    {
        //remove type tables
        $tableTypes = ['datetime', 'decimal', 'int', 'text', 'varchar'];
        foreach ($tableTypes as $type) {
            $this->setup->getConnection()->dropTable(
                $this->setup->getTable(PostSetup::ENTITY_TYPE_CODE . '_' . $type)
            );
        }
        //remove main table
        $this->setup->getConnection()->dropTable(
            $this->setup->getTable(PostSetup::ENTITY_TYPE_CODE)
        );
        //remove eav attributes table
		$this->setup->getConnection()->dropTable(
            $this->setup->getTable(PostSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute')
        );
    }

    /**
     * create eav attributes tables and add foreign keys
     */
    protected function createEAVMainTable()
    {
        $tableName = PostSetup::EAV_ENTITY_TYPE_CODE . '_eav_attribute';

        $table = $this->setup->getConnection()->newTable(
            $this->setup->getTable($tableName)
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute Id'
            )->addColumn(
                'is_global',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Is Global'
            )->addColumn(
                'is_filterable',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable'
            )->addColumn(
                'is_visible',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Is Visible'
            )->addColumn(
                'is_wysiwyg_enabled',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute uses WYSIWYG'
            )->addColumn(
                'validate_rules',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Validate Rules'
            )->addColumn(
                'is_system',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is System'
            /*)->addColumn( // seems is not necesary because this value is in eav_entity_attribute
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sort Order'*/
            )->addColumn(
                'data_model',
                Table::TYPE_TEXT,
                255,
                [],
                'Data Model'
            )->addForeignKey(
                $this->setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $this->setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->setComment(
                'OAG Blog Eav Attribute'
            );
        $this->setup->getConnection()->createTable($table);
    }

    /**
     * create eav entities tables and add foreign keys
     */
    protected function createEntityTable($entityCode, $type, $valueType, $valueLength = null)
    {
        $tableName = $entityCode . '_' . $type;

        $table = $this->setup->getConnection()
            ->newTable($this->setup->getTable($tableName))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            /*->addColumn( // Is not necessary because main entity table has this value
                'entity_type_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity Type ID'
            )*/
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )
            ->addColumn(
                'value',
                $valueType,
                $valueLength,
                [],
                'Value'
            )
            ->addIndex(
                $this->setup->getIdxName(
                    $tableName,
                    ['entity_id', 'attribute_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $this->setup->getIdxName($tableName, ['entity_id']),
                ['entity_id']
            )
            ->addIndex(
                $this->setup->getIdxName($tableName, ['attribute_id']),
                ['attribute_id']
            )
            ->addIndex(
                $this->setup->getIdxName($tableName, ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $this->setup->getFkName(
                    $tableName,
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $this->setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $this->setup->getFkName(
                    $tableName,
                    'entity_id',
                    $entityCode,
                    'entity_id'
                ),
                'entity_id',
                $this->setup->getTable($entityCode),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $this->setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                'store_id',
                $this->setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment($entityCode . ' ' . $type . 'Attribute Backend Table');
        $this->setup->getConnection()->createTable($table);
    }
}
