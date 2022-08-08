<?php
/**
 * install entities 
 */

namespace OAG\Blog\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use OAG\Blog\Setup\PostSetupFactory;

class InstallData implements InstallDataInterface
{

    /**
     * Post setup factory
     *
     * @var PostSetupFactory
     */
    protected $postSetupFactory;

    /**
     * Init
     *
     * @param PostSetupFactory $postSetupFactory
     */
    public function __construct(
        PostSetupFactory $postSetupFactory
    ) {
        $this->postSetupFactory = $postSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var PostSetup $postSetup */
        $postSetup = $this->postSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();
        $postSetup->installEntities();

        /**
         * This params needs to be updated here because the attribute "is_pagebuilder_enabled"
         * is not configured in any PropertyMapper. You can see an example in
         * Magento\Catalog\Model\ResourceModel\Setup\PropertyMapper and di.xml in catalog module
         *
         * Also, We don't want to create a custom PropertyMapper bacause seems that this affect to
         * all EAV install maps. You can see in Magento\Eav\Model\Entity\Setup\PropertyMapper\Composite
         * in map function that has an foreach for propertyMappers property that check all di.xml
         * PropertyMapper\Composite classes configured.
         */
        $postSetup->updateAttribute(
            PostSetup::ENTITY_TYPE_CODE,
            'content',
            [
                'is_pagebuilder_enabled' => 1
            ]
        );
        $setup->endSetup();
    }
}
