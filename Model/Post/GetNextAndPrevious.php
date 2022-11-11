<?php

namespace OAG\Blog\Model\Post;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use OAG\Blog\Model\ResourceModel\Post\CollectionFactory;
use OAG\Blog\Api\Data\PostInterface;

class GetNextAndPrevious
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
     * Init dependencies
     *
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SortOrderBuilder $sortOrderBuilder
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
     * Get Previous Post
     *
     * @param PostInterface $currentPost
     * @return PostInterface|null
     */
    public function getPreviousPost(PostInterface $currentPost): ?PostInterface
    {
        return $this->getNextOrPrevious($currentPost, 'lt', SortOrder::SORT_DESC);
    }

    /**
     * Get Next Post
     *
     * @param PostInterface $currentPost
     * @return PostInterface|null
     */
    public function getNextPost(PostInterface $currentPost): ?PostInterface
    {
        return $this->getNextOrPrevious($currentPost, 'gt', SortOrder::SORT_ASC);
    }


    /**
     * Get Previous or Next post depends of published at filter
     *
     * @param PostInterface $currentPost
     * @param string $publishedAtFilter
     * @param string $publishAtOrder SortOrder::SORT_ASC|SortOrder::SORT_DESC
     * @return PostInterface|null
     */
    protected function getNextOrPrevious(
        PostInterface $currentPost,
        string $publishedAtFilter,
        string $publishAtOrder): ?PostInterface
    {
        if (!$currentPost->getPublishedAt()) {
            return null;
        }

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect([
            PostInterface::KEY_TITLE,
            PostInterface::KEY_URL_KEY,
            PostInterface::KEY_STATUS
        ]);

        $searchCriteriaBuilder = $this->searchCriteriaBuilder
            ->addFilter(PostInterface::KEY_STATUS, Boolean::VALUE_YES)
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilder
            ->addFilter(PostInterface::KEY_STATUS, Boolean::VALUE_YES)
            ->addFilter(PostInterface::KEY_PUBLISHED_AT, $currentPost->getPublishedAt(), $publishedAtFilter)
            ->create();

        $searchCriteriaBuilder->setSortOrders([
            $this->sortOrderBuilder->setField(PostInterface::KEY_PUBLISHED_AT)->setDirection($publishAtOrder)->create()
        ]);

        //We only want the first one, so we will set limit in the query
        $searchCriteriaBuilder->setPageSize(1);

        $this->collectionProcessor->process(
            $searchCriteriaBuilder,
            $collection
        );

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        } else {
            return null;
        }
    }
}
