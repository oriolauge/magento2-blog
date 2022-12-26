<?php
/**
 * Instalation Schema
 */

namespace OAG\Blog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Setup\EavTablesSetupFactory;
use OAG\Blog\Setup\PostSetup;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var EavTablesSetupFactory
     */
    protected $eavTablesSetupFactory;

    /**
     * Init
     *
     * @internal param EavTablesSetupFactory $EavTablesSetupFactory
     */
    public function __construct(
        EavTablesSetupFactory $eavTablesSetupFactory
    ) {
        $this->eavTablesSetupFactory = $eavTablesSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = PostSetup::ENTITY_TABLE_CODE;
        /**
         * Create entity Table
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable($tableName))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->setComment('Entity Table');

        $table->addColumn(
            PostInterface::KEY_ATTR_TYPE_ID,
            Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
            ],
            'Attribute Set ID'
        )->addIndex(
            $setup->getIdxName($tableName, [PostInterface::KEY_ATTR_TYPE_ID]),
            [PostInterface::KEY_ATTR_TYPE_ID]
        )->addForeignKey(
            $setup->getFkName(
                'oag_blog_post',
                PostInterface::KEY_ATTR_TYPE_ID,
                'eav_attribute_set',
                PostInterface::KEY_ATTR_TYPE_ID
            ),
            PostInterface::KEY_ATTR_TYPE_ID,
            $setup->getTable('eav_attribute_set'),
            PostInterface::KEY_ATTR_TYPE_ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        // Add more static attributes here...

        $table->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            PostInterface::KEY_UPDATED_AT,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        );

        $setup->getConnection()->createTable($table);

        /** @var \OAG\Blog\Setup\EavTablesSetup $eavTablesSetup */
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
        $eavTablesSetup->createEavTables(PostSetup::ENTITY_TABLE_CODE);

        $setup->endSetup();
    }
}
