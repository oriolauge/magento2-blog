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
        $entities = $postSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $postSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();
    }
}
