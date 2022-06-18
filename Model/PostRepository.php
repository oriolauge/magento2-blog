<?php

namespace OAG\Blog\Model;

use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Model\PostFactory;
use OAG\Blog\Model\ResourceModel\Post as PostResource;
use OAG\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * Construct function
     *
     * @param PostFactory $postFactory
     * @param PostResource $postResource
     */
    public function __construct(
        PostFactory $postFactory,
        PostResource $postResource
    ) {
        $this->postFactory = $postFactory;
        $this->postResource = $postResource;
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
                throw new NoSuchEntityException(__('Unable to find post with ID "%1"', $id));
            }
            $this->instances[$id][$cacheKey] = $post;
        }
        return $this->instances[$id][$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public function save(PostInterface $post)
    {
        $this->postResource->save($post);
        return $post;
    }

    /**
     * @inheritdoc
     */
    public function delete(PostInterface $post)
    {
        $this->postResource->delete($post);
    }
}
