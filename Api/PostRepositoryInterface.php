<?php

namespace OAG\Blog\Api;
use Magento\Framework\Api\SearchCriteriaInterface;
use OAG\Blog\Api\Data\PostInterface;

interface PostRepositoryInterface
{
    /**
     * @param int $id
     * @param int $storeId
     * @return PostInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id, $storeId = null);

    /**
     * Get a new post with empty data (not inizializate)
     *
     * @param int $storeId
     * @return PostInterface
     */
    public function createEmptyPost($storeId = null);
 
    /**
     * @param PostInterface $post
     * @return PostInterface
     */
    public function save(PostInterface $post);
 
    /**
     * @param PostInterface $post
     * @return void
     */
    public function delete(PostInterface $post);

    /**
     * Get post list
     *
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}