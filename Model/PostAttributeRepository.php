<?php
namespace OAG\Blog\Model;
use OAG\Blog\Api\PostAttributeRepositoryInterface;
use OAG\Blog\Api\Data\PostAttributeInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;

/**
 * Post attribute repository
 * 
 * Based on Magento\Catalog\Model\Product\Attribute\Repository
 */
class PostAttributeRepository implements PostAttributeRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     *
     * @param AttributeRepositoryInterface $eavAttributeRepository
     */
    public function __construct(
        AttributeRepositoryInterface $eavAttributeRepository
    ) {
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            PostAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }
}
