<?php
declare(strict_types=1);

namespace OAG\Blog\Model;
use OAG\Blog\Api\PostAttributeGroupRepositoryInterface;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;

/**
 * Class \OAG\Blog\Model\PostAttributeGroupRepository
 */
class PostAttributeGroupRepository implements PostAttributeGroupRepositoryInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeGroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @param AttributeGroupRepositoryInterface $groupRepository
     */
    public function __construct(
        AttributeGroupRepositoryInterface $groupRepository
    ) {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->groupRepository->getList($searchCriteria);
    }
}
