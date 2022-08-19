<?php
namespace OAG\Blog\Setup;

use Magento\CatalogGraphQl\Model\Category\Filter\SearchCriteria;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Type;
use OAG\Blog\Setup\EavTablesSetupFactory;
use OAG\Blog\Setup\PostSetup;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

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

    /**
     * @var BookmarkRepositoryInterface
     */
    protected $bookmarkRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Construct function
     *
     * @param CollectionFactory $eavCollectionFactory
     * @param Type $eavResourceModel
     * @param EavTablesSetupFactory $eavTablesSetupFactory
     * @param BookmarkRepositoryInterface $bookmarkRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        CollectionFactory $eavCollectionFactory,
        Type $eavResourceModel,
        EavTablesSetupFactory $eavTablesSetupFactory,
        BookmarkRepositoryInterface $bookmarkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    )
    {
        $this->eavCollectionFactory = $eavCollectionFactory;
        $this->eavResourceModel     = $eavResourceModel;
        $this->eavTablesSetupFactory = $eavTablesSetupFactory;
        $this->bookmarkRepository = $bookmarkRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Uninstall all data module
     *
     * We don't add startSetup/endSetup because in uninstall, we use foreign keys
     * to remove attributes, attributes_grous, etc
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
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

        /**
         * Remove data from ui_bookmark table.
         * This table has user admin columns configurations in post
         * grid admin page
         */
        $bookmarkUi = $this->bookmarkRepository->getList(
            $this->searchCriteriaBuilder->addFilters([
                $this->filterBuilder->setField('namespace')
                    ->setConditionType('eq')
                    ->setValue('oag_blog_post_listing')
                    ->create()
            ]
            )->create()
        );
        foreach ($bookmarkUi->getItems() as $bookmark) {
            $this->bookmarkRepository->delete($bookmark);
        }
    }

}