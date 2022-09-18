<?php

namespace OAG\Blog\Model;

use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Model\PostFactory;
use OAG\Blog\Model\ResourceModel\Post as PostResource;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\ResourceModel\Post\CollectionFactory;
use OAG\Blog\Api\Data\PostSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use PhpParser\Node\Expr\PostInc;

/**
 * Repository for Post.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * @var Post[]
     */
    protected $instances = [];
    
    /**
     * @var PostFactory
     */
    protected $postFactory;
 
    /**
     * @var PostResource
     */
    protected $postResource;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var PostSearchResultsInterfaceFactory
     */
    protected $postSearchResultsFactory;
 
    /**
     * Construct function
     *
     * @param PostFactory $postFactory
     * @param PostResource $postResource
     */
    public function __construct(
        PostFactory $postFactory,
        PostResource $postResource,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        PostSearchResultsInterfaceFactory $postSearchResultsFactory
    ) {
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->postSearchResultsFactory = $postSearchResultsFactory;
    }


    /**
     * @inheritdoc
     */
    public function getById($id, $storeId = null)
    {
        $cacheKey = $storeId ?? 'all';
        if (!isset($this->instances[$id][$cacheKey])) {
            $post = $this->postFactory->create();
            if (null !== $storeId) {
                $post->setStoreId($storeId);
            }
            $this->postResource->load($post, $id);
            if (!$post->getId()) {
                throw NoSuchEntityException::singleField('id', $id);
            }
            $this->instances[$id][$cacheKey] = $post;
        }
        return $this->instances[$id][$cacheKey];
    }

    /**
     * @inheritDoc
     */
    public function createEmptyPost($storeId = null)
    {
        $post = $this->postFactory->create();
        if (null !== $storeId) {
            $post->setStoreId($storeId);
        }
        return $post;
    }

    /**
     * @inheritDoc
     */
    public function save(PostInterface $post)
    {
        $this->postResource->save($post);
        return $post;
    }

    /**
     * @inheritDoc
     */
    public function delete(PostInterface $post)
    {
        $this->postResource->delete($post);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        //@todo: try to improve this...
        $collection->addAttributeToSelect([
            PostInterface::KEY_TITLE,
            PostInterface::KEY_URL_KEY,
            PostInterface::KEY_LIST_IMAGE,
            PostInterface::KEY_LIST_IMAGE_ALT,
            PostInterface::KEY_SHORT_CONTENT,
            PostInterface::KEY_PUBLISHED_AT,
            PostInterface::KEY_STATUS
        ]);
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->postSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
