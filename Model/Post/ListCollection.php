<?php

namespace OAG\Blog\Model\Post;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use OAG\Blog\Model\ResourceModel\Post\CollectionFactory;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\ResourceModel\Post\Collection;

class ListCollection
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * Construct function
     *
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Get post list collection
     *
     * @param integer|null $pageSize
     * @param integer $page
     * @return Collection
     */
    public function getPostListCollection(int $pageSize = null, int $page = 1): Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect([
            PostInterface::KEY_TITLE,
            PostInterface::KEY_URL_KEY,
            PostInterface::KEY_LIST_IMAGE,
            PostInterface::KEY_LIST_IMAGE_ALT,
            PostInterface::KEY_SHORT_CONTENT,
            PostInterface::KEY_PUBLISHED_AT,
            PostInterface::KEY_STATUS
        ]);

        $searchCriteriaBuilder = $this->searchCriteriaBuilder
            ->addFilter(PostInterface::KEY_STATUS, Boolean::VALUE_YES)
            ->create();

        $searchCriteriaBuilder->setSortOrders([
            $this->sortOrderBuilder->setField(PostInterface::KEY_PUBLISHED_AT)->setDirection(SortOrder::SORT_DESC)->create()
        ]);

        if (is_numeric($pageSize) && is_numeric($page)) {
            $searchCriteriaBuilder->setPageSize($pageSize)->setCurrentPage($page);
        }

        $this->collectionProcessor->process(
            $searchCriteriaBuilder,
            $collection
        );

        return $collection;
    }
}