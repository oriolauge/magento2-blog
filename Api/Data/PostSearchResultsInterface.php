<?php

namespace OAG\Blog\Api\Data;
use Magento\Framework\Api\SearchResultsInterface;
 
interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \OAG\Blog\Api\Data\PostInterface[]
     */
    public function getItems();
 
    /**
     * @param \OAG\Blog\Api\Data\PostInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
