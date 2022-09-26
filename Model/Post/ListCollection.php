<?php

namespace OAG\Blog\Model\Post;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * Construct function
     *
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Get post list collection
     *
     * @return Collection
     */
    public function getPostListCollection(int $pageSize, int $page = 1): Collection
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

        $this->collectionProcessor->process($this->searchCriteriaBuilder
            ->addFilter(PostInterface::KEY_STATUS, Boolean::VALUE_YES)
            ->setPageSize($pageSize)
            ->setCurrentPage($page)
            ->create()
            , $collection
        );

        return $collection;
    }
}