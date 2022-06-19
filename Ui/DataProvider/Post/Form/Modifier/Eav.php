<?php
namespace OAG\Blog\Ui\DataProvider\Post\Form\Modifier;

use Exception;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use OAG\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Api\PostRepositoryInterface;
use Magento\Store\Model\Store;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Framework\App\ObjectManager;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use OAG\Blog\Api\PostAttributeGroupRepositoryInterface;

/**
 * Class Eav data provider for post editing form
 * 
 * based on Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav
 */
class Eav implements ModifierInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var EavAttribute[]
     */
    protected $attributes = [];

    /**
     * @var AttributeGroupInterface[]
     */
    protected $attributeGroups = [];

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var PostAttributeGroupRepositoryInterface
     */
    protected $attributeGroupRepository;

    public function __construct(
        CollectionFactory $collection,
        RequestInterface $request,
        PostRepositoryInterface $postRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostAttributeGroupRepositoryInterface $attributeGroupRepository,
        AttributeCollectionFactory $attributeCollectionFactory = null
    )
    {
        $this->collection = $collection->create();
        $this->request = $request;
        $this->postRepository = $postRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->attributeCollectionFactory = $attributeCollectionFactory
            ?: ObjectManager::getInstance()->get(AttributeCollectionFactory::class);
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];
            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = 'data.post';
                $meta[$groupCode]['arguments']['data']['config']['label'] = __($group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = false;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] = 10;
            }
        }

        return $meta;
    }

    /**
     * Get attributes meta
     *
     * @param string $groupCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributesMeta(array $attributes, $groupCode)
    {
        $meta = [];
        $order = 0;
        foreach ($attributes as $sortOrder => $attribute) {
            //$meta[$attribute->getAttributeCode()]['arguments']['data']['config']['service']['template'] = 'ui/form/element/helper/service';
            //$meta[$attribute->getAttributeCode()]['arguments']['data']['config']['disabled'] = 1;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['componentType'] = Field::NAME;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['dataType'] = $attribute->getFrontendInput();
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['label'] = $attribute->getFrontendLabel();
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['formElement'] = 'input';
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['source'] = $groupCode;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['visible'] = true;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['required'] = $attribute->getIsRequired();
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['notice'] = null;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['default'] = null;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['code'] = $attribute->getAttributeCode();
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['dataScope'] = $attribute->getAttributeCode();
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['sortOrder'] = $order;
            $order++;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['validation'] = ['required-entry' => true];
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['globalScope'] = false;
            $meta[$attribute->getAttributeCode()]['arguments']['data']['config']['scopeLabel'] = __('[store view]');

        }
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $post = $this->getCurrentPost();
        if ($post) {
            $data[$post->getId()]['post'] = $post->getData();
        }
        return $data;
    }

    /**
     * Retrieve attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        if (!$this->attributes) {
            $this->attributes = $this->loadAttributesForGroups($this->getGroups());
        }

        return $this->attributes;
    }

    /**
     * Loads attributes for specified groups at once
     *
     * @param AttributeGroupInterface[] $groups
     * @return PostAttributeInterface[]
     */
    protected function loadAttributesForGroups(array $groups)
    {
        $attributes = [];
        $groupIds = [];

        foreach ($groups as $group) {
            $groupIds[$group->getAttributeGroupId()] = $this->calculateGroupCode($group);
            $attributes[$this->calculateGroupCode($group)] = [];
        }

        $collection = $this->attributeCollectionFactory->create();
        $collection->setAttributeGroupFilter(array_keys($groupIds));
        foreach ($collection->getItems() as $attribute) {
            $attributeCode = $groupIds[$attribute->getAttributeGroupId()];
            $attributes[$attributeCode][] = $attribute;
        }

        return $attributes;
    }

    /**
     * Retrieve groups
     *
     * @return AttributeGroupInterface[]
     */
    protected function getGroups()
    {
        if (!$this->attributeGroups) {
            $searchCriteria = $this->prepareGroupSearchCriteria()->create();
            $attributeGroupSearchResult = $this->attributeGroupRepository->getList($searchCriteria);
            foreach ($attributeGroupSearchResult->getItems() as $group) {
                $this->attributeGroups[$this->calculateGroupCode($group)] = $group;
            }
        }

        return $this->attributeGroups;
    }

    /**
     * Calculate group code based on group name.
     *
     * Seems Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav::calculateGroupCode()
     * will change after MAGETWO-48290 is complete, so we will maintan this function but we
     * will change some code to adapt to post requeriments.
     *
     * @see Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav::calculateGroupCode()
     * @param AttributeGroupInterface $group
     * @return string
     */
    private function calculateGroupCode(AttributeGroupInterface $group)
    {
        return $group->getAttributeGroupCode();
    }

    /**
     * Initialize attribute group search criteria with filters.
     *
     * @return SearchCriteriaBuilder
     */
    protected function prepareGroupSearchCriteria()
    {
        return $this->searchCriteriaBuilder->addFilter(
            AttributeGroupInterface::ATTRIBUTE_SET_ID,
            $this->getAttributeSetId()
        );
    }

    /**
     * Return current attribute set id
     *
     * @return int|null
     */
    protected function getAttributeSetId()
    {
        return $this->getCurrentPost()->getAttributeSetId();
    }

    /**
     * Get current editing post
     * 
     * We use this method because Registry class is deprecated
     */
    protected function getCurrentPost(): PostInterface
    {
        if (!($id = $this->request->getParam('entity_id'))) {
            throw new Exception(__('URL entity_id param is missign'));
        }

        $storeId = (int) $this->request->getParam('store');

        if ($storeId != Store::DEFAULT_STORE_ID) {
            return $this->postRepository->getById($id, $storeId);
        } else {
            return $this->postRepository->getById($id);
        }
    }
}
