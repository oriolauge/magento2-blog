<?php

namespace OAG\Blog\Api;

/**
 * Interface RepositoryInterface must be implemented in new model
 * @api
 */
interface PostAttributeRepositoryInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductAttributeSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
