<?php
namespace OAG\Blog\Api;

/**
 * @api
 */
interface PostAttributeGroupRepositoryInterface
{
    /**
     * Retrieve list of attribute groups
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeGroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
