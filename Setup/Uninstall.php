<?php
namespace OAG\Blog\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Type;
use OAG\Blog\Setup\EavTablesSetupFactory;
use OAG\Blog\Setup\PostSetup;


class Uninstall implements UninstallInterface
{
    /**
     * @var CollectionFactory
     */
    protected $eavCollectionFactory;
    /**
     * @var Type
     */
    protected $eavResourceModel;
    /**
     * @var EavTablesSetupFactory
     */
    protected $eavTablesSetupFactory;

    public function __construct(
        CollectionFactory $eavCollectionFactory,
        Type $eavResourceModel,
        EavTablesSetupFactory $eavTablesSetupFactory
    )
    {
        $this->eavCollectionFactory = $eavCollectionFactory;
        $this->eavResourceModel     = $eavResourceModel;
        $this->eavTablesSetupFactory = $eavTablesSetupFactory;
    }

    /**
     * Uninstall all data module
     *
     * @todo: Removes attributes and group attributes
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
		$setup->startSetup();
        $eavTypeCollection = $this->eavCollectionFactory->create();
        $eavTypeCollection->addFieldToFilter('entity_type_code',
            [
                'in' => [PostSetup::ENTITY_TYPE_CODE]
            ]
        );
        foreach ($eavTypeCollection as $eavType) {
            $this->eavResourceModel->delete($eavType);
        }
        $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
        $eavTablesSetup->dropEavTables();
		$setup->endSetup();
    }

}