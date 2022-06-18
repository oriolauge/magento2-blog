<?php

namespace OAG\Blog\Api;
use OAG\Blog\Api\Data\PostInterface;

interface PostRepositoryInterface
{
    /**
     * @param int $id
     * @param int $storeId
     * @return \OAG\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id, $storeId = null);
 
    /**
     * @param \OAG\Blog\Api\Data\PostInterface $post
     * @return \OAG\Blog\Api\Data\PostInterface
     */
    public function save(PostInterface $post);
 
    /**
     * @param \OAG\Blog\Api\Data\PostInterface $post
     * @return void
     */
    public function delete(PostInterface $post);
}