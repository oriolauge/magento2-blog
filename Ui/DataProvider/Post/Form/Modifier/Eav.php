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
use Magento\Framework\Api\SortOrderBuilder;
use OAG\Blog\Api\Data\PostAttributeInterface;
use OAG\Blog\Api\PostAttributeRepositoryInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Magento\Store\Model\StoreManagerInterface;
use OAG\Blog\Api\Data\EavAttributeInterface;
use OAG\Blog\Model\Attribute\ScopeOverriddenValue;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use OAG\Blog\Ui\DataProvider\EavValidationRules;
use OAG\Blog\Model\PostFactory;
use OAG\Blog\Model\Post\Attribute\Backend\Image as ImageBackendModel;
use OAG\Blog\Model\Post\FileInfo;
use OAG\Blog\Model\Post\Image;
use Magento\Backend\Model\Session;

/**
 * Class Eav data provider for post editing form
 * 
 * based on Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav
 */
class Eav implements ModifierInterface
{
    /**
     * Holds sort order multipier
     */
    const SORT_ORDER_MULTIPLIER = 10;

    /**
     * Holds post data scope
     */
    const DATA_SCOPE_PRODUCT = 'data';

    /**
     * Holds container prefix for meta attributes
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * Holds Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

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

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var PostAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var FormElementMapper
     */
    protected $formElementMapper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $canDisplayUseDefault = [];

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var EavValidationRules
     */
    protected $eavValidationRules;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var Image
     */
    private $postImage;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Save new post with tmp form data to show in edit form
     *
     * @var PostInterface
     */
    protected $newPost;

    public function __construct(
        CollectionFactory $collection,
        RequestInterface $request,
        PostRepositoryInterface $postRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostAttributeGroupRepositoryInterface $attributeGroupRepository,
        SortOrderBuilder $sortOrderBuilder,
        PostAttributeRepositoryInterface $attributeRepository,
        ArrayManager $arrayManager,
        FormElementMapper $formElementMapper,
        StoreManagerInterface $storeManager,
        ScopeOverriddenValue $scopeOverriddenValue,
        EavValidationRules $eavValidationRules,
        PostFactory $postFactory,
        FileInfo $fileInfo,
        Image $postImage,
        Session $session,
        AttributeCollectionFactory $attributeCollectionFactory = null
    )
    {
        $this->collection = $collection->create();
        $this->request = $request;
        $this->postRepository = $postRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->arrayManager = $arrayManager;
        $this->formElementMapper = $formElementMapper;
        $this->storeManager = $storeManager;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->eavValidationRules = $eavValidationRules;
        $this->postFactory = $postFactory;
        $this->fileInfo = $fileInfo;
        $this->postImage = $postImage;
        $this->session = $session;
        $this->attributeCollectionFactory = $attributeCollectionFactory
            ?: ObjectManager::getInstance()->get(AttributeCollectionFactory::class);
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = 0;
        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];
            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_PRODUCT;
                $meta[$groupCode]['arguments']['data']['config']['label'] = __($group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = $group->getDefaultId() ? false : true;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] =
                    $sortOrder * self::SORT_ORDER_MULTIPLIER;
            }
            $sortOrder++;
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
    protected function getAttributesMeta(array $attributes, $groupCode)
    {

        $meta = [];
        foreach ($attributes as $sortOrder => $attribute) {
            if (!($attributeContainer = $this->setupAttributeContainerMeta($attribute))) {
                continue;
            }

            $attributeContainer = $this->addContainerChildren($attributeContainer, $attribute, $groupCode, $sortOrder);
            $meta[self::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $attributeContainer;
        }

        return $meta;
    }

    /**
     * Add container children
     * 
     * @param array $attributeContainer
     * @param EavAttributeInterface $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     */
    protected function addContainerChildren(
        array $attributeContainer,
        EavAttributeInterface $attribute,
        $groupCode,
        $sortOrder
    ) {
        foreach ($this->getContainerChildren($attribute, $groupCode, $sortOrder) as $childCode => $child) {
            $attributeContainer['children'][$childCode] = $child;
        }

        $attributeContainer = $this->arrayManager->merge(
            ltrim(self::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER),
            $attributeContainer,
            [
                'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER
            ]
        );

        return $attributeContainer;
    }

    /**
     * Initial meta setup
     *
     * @param EavAttributeInterface $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setupAttributeMeta(EavAttributeInterface $attribute, $groupCode, $sortOrder)
    {
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);
        $attributeCode = $attribute->getAttributeCode();
        $meta = $this->arrayManager->set(
            $configPath,
            [],
            [
                'dataType' => $attribute->getFrontendInput(),
                'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
                'visible' => $attribute->getIsVisible(),
                'required' => $attribute->getIsRequired(),
                'notice' => $attribute->getNote() === null ? null : __($attribute->getNote()),
                'default' => (!$this->isPostExists()) ? $attribute->getDefaultValue() : null,
                'label' => __($attribute->getDefaultFrontendLabel()),
                'code' => $attributeCode,
                'source' => $groupCode,
                'scopeLabel' => $this->getScopeLabel($attribute),
                'globalScope' => $this->isScopeGlobal($attribute),
                'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER
            ]
        );

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['componentType' => Field::NAME]);
        }

        $childData = $this->arrayManager->get($configPath, $meta, []);
        if ($rules = $this->eavValidationRules->build($attribute, $childData)) {
            $meta = $this->arrayManager->merge($configPath, $meta, ['validation' => $rules]);
        }

        $meta = $this->addUseDefaultValueCheckbox($attribute, $meta);

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $meta = $this->customizeCheckbox($attribute, $meta);
                break;
            case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            case 'datetime':
                $meta = $this->customizeDatetimeAttribute($meta);
                break;
            case 'image':
                $meta = $this->customizeMediaImage($attribute, $meta);
                break;
        }

        return $meta;
    }

    /**
     * Customize media attribute
     *
     * @param array $meta
     * @return array
     */
    private function customizeMediaImage(EavAttributeInterface $attribute, array $meta): array
    {
        $meta['arguments']['data']['config']['formElement'] = 'fileUploader';
        $meta['arguments']['data']['config']['dataType'] = 'string';
        $meta['arguments']['data']['config']['elementTmpl'] = 'ui/form/element/uploader/uploader';
        $meta['arguments']['data']['config']['previewTmpl'] = 'Magento_Catalog/image-preview';
        $meta['arguments']['data']['config']['uploaderConfig']['url'] = 'oag_blog/post/upload/param_name/' . $attribute->getAttributeCode();
        return $meta;
    }

    /**
     * Customize datetime attribute
     *
     * @param array $meta
     * @return array
     */
    private function customizeDatetimeAttribute(array $meta): array
    {
        $meta['arguments']['data']['config']['options']['showsTime'] = 1;

        return $meta;
    }

    /**
     * Add wysiwyg properties
     *
     * @param EavAttributeInterface $attribute
     * @param array $meta
     * @return array
     */
    private function customizeWysiwyg(EavAttributeInterface $attribute, array $meta)
    {
        if (!$attribute->getIsWysiwygEnabled()) {
            return $meta;
        }

        $meta['arguments']['data']['config']['formElement'] = WysiwygElement::NAME;
        $meta['arguments']['data']['config']['wysiwyg'] = true;
        /**
         * @todo: you need to improve this array to make more
         * customizable with attribute values
         */
        $meta['arguments']['data']['config']['wysiwygConfigData'] = [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'admin__field-wide',
            'is_pagebuilder_enabled' => true,
            'pagebuilder_content_snapshot' => true,
            'pagebuilder_button' => true
        ];

        return $meta;
    }

    /**
     * Customize checkboxes
     *
     * @param EavAttributeInterface $attribute
     * @param array $meta
     * @return array
     */
    private function customizeCheckbox(EavAttributeInterface $attribute, array $meta)
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta['arguments']['data']['config']['prefer'] = 'toggle';
            $meta['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }

        return $meta;
    }

    /**
     * Adds 'use default value' checkbox.
     *
     * @param EavAttributeInterface $attribute
     * @param array $meta
     * @return array
     */
    private function addUseDefaultValueCheckbox(EavAttributeInterface $attribute, array $meta)
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);
        if ($canDisplayService) {
            $meta['arguments']['data']['config']['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            $meta['arguments']['data']['config']['disabled'] = !$this->scopeOverriddenValue->containsValue(
                PostInterface::class,
                $this->getCurrentPost(),
                $attribute->getAttributeCode(),
                (int) $this->request->getParam('store', 0)
            );
        }
        return $meta;
    }

    /**
     * Whether attribute can have default value
     *
     * @param EavAttributeInterface $attribute
     * @return bool
     */
    private function canDisplayUseDefault(EavAttributeInterface $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        /** @var Post $post */
        $post = $this->getCurrentPost();

        if (isset($this->canDisplayUseDefault[$attributeCode])) {
            return $this->canDisplayUseDefault[$attributeCode];
        }

        return $this->canDisplayUseDefault[$attributeCode] = (
            ($attribute->getScope() != EavAttributeInterface::SCOPE_GLOBAL_TEXT)
            && $post
            && $post->getId()
            && $post->getStoreId()
        );
    }

    /**
     * Retrieve container child fields
     * 
     * @param EavAttributeInterface $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     */
    public function getContainerChildren(EavAttributeInterface $attribute, $groupCode, $sortOrder)
    {
        if (!($child = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder))) {
            return [];
        }

        return [$attribute->getAttributeCode() => $child];
    }

    /**
     * Setup attribute container meta
     *
     * @param EavAttributeInterface $attribute
     * @return array
     */
    protected function setupAttributeContainerMeta(EavAttributeInterface $attribute)
    {
        $containerMeta = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'formElement' => 'container',
                'componentType' => 'container',
                'breakLine' => false,
                'label' => $attribute->getDefaultFrontendLabel(),
                'required' => $attribute->getIsRequired(),
            ]
        );

        if ($attribute->getIsWysiwygEnabled()) {
            $containerMeta = $this->arrayManager->merge(
                'arguments/data/config',
                $containerMeta,
                [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'label' => false,
                    'required' => false,
                ]
            );
        }

        return $containerMeta;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        $post = $this->getCurrentPost();
        if ($post) {
            $postData = $post->getData();
            $postData = $this->convertValues($post, $postData);
            $data[$post->getId()] = $postData;
        }
        return $data;
    }

    /**
     * Converts post image data to acceptable for rendering format
     *
     * @param PostInterface $post
     * @param array $postData
     * @return array
     */
    protected function convertValues(PostInterface $post, $postData): array
    {
        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];
            if ($attributes) {
                $postData = $this->getAttributeData($attributes, $postData, $post);
            }
        }
        return $postData;
    }

    protected function getAttributeData(
        array $attributes,
        array $postData,
        PostInterface $post
    ): array
    {
        foreach ($attributes as $sortOrder => $attribute) {
            if ($attribute->getBackend() instanceof ImageBackendModel) {
                $attributeCode = $attribute->getAttributeCode();
                $fileName = $post->getData($attributeCode);
                if ($fileName && $this->fileInfo->isExist($fileName)) {
                    unset($postData[$attributeCode]);
                    $stat = $this->fileInfo->getStat($fileName);
                    $mime = $this->fileInfo->getMimeType($fileName);
                    $postData[$attributeCode][0]['name'] = basename($fileName);
                    $postData[$attributeCode][0]['url'] = $this->postImage->getUrl($post, $attributeCode);
                    $postData[$attributeCode][0]['size'] = $stat['size'];
                    $postData[$attributeCode][0]['type'] = $mime;
                }
            }
        }

        return $postData;
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

        $mapAttributeToGroup = [];

        foreach ($collection->getItems() as $attribute) {
            $mapAttributeToGroup[$attribute->getAttributeId()] = $attribute->getAttributeGroupId();
        }

        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setAscendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AttributeGroupInterface::GROUP_ID, array_keys($groupIds), 'in')
            ->addFilter(PostAttributeInterface::IS_VISIBLE, 1)
            ->addSortOrder($sortOrder)
            ->create();

        $groupAttributes = $this->attributeRepository->getList($searchCriteria)->getItems();

        foreach ($groupAttributes as $attribute) {
            $attributeGroupId = $mapAttributeToGroup[$attribute->getAttributeId()];
            $attributeGroupCode = $groupIds[$attributeGroupId];
            $attributes[$attributeGroupCode][] = $attribute;
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
     * @return int
     */
    protected function getAttributeSetId(): int
    {
        $post = $this->getCurrentPost();
        if (is_numeric($post->getAttributeSetId())) {
            return $post->getAttributeSetId();
        } else {
            /**
             * In case user are created a new post, we need to get the
             * default attribute set
             */
            return $post->getDefaultAttributeSetId();
        }
    }

    /**
     * Get current editing post
     * 
     * We use this method because Registry class is deprecated
     */
    protected function getCurrentPost(): PostInterface
    {
        /**
         * For create new post option
         */
        if (!($id = $this->request->getParam('entity_id'))) {
            if (!$this->newPost) {
                $this->newPost = $this->postRepository->createEmptyPost();

                $data = $this->session->getFormData(true);
                //var_dump($data); // die("sdsad");
                if (!empty($data)) {
                    $this->newPost->addData($data);
                }
            }
            return $this->newPost;
        }

        $storeId = (int) $this->request->getParam('store');

        if ($storeId != Store::DEFAULT_STORE_ID) {
            return $this->postRepository->getById($id, $storeId);
        } else {
            return $this->postRepository->getById($id);
        }
    }

    /**
     * Retrieve form element
     *
     * @param string $value
     * @return mixed
     */
    private function getFormElementsMapValue($value)
    {
        $valueMap = $this->formElementMapper->getMappings();

        return $valueMap[$value] ?? $value;
    }

    /**
     * Check is post already new or we trying to create one
     *
     * @return bool
     */
    private function isPostExists()
    {
        return (bool) $this->getCurrentPost()->getId();
    }

    /**
     * Retrieve scope label
     *
     * @param EavAttributeInterface $attribute
     * @return \Magento\Framework\Phrase|string
     */
    private function getScopeLabel(EavAttributeInterface $attribute)
    {
        if ($this->storeManager->isSingleStoreMode()) {
            return '';
        }

        switch ($attribute->getScope()) {
            case PostAttributeInterface::SCOPE_GLOBAL_TEXT:
                return __('[GLOBAL]');
            case PostAttributeInterface::SCOPE_WEBSITE_TEXT:
                return __('[WEBSITE]');
            case PostAttributeInterface::SCOPE_STORE_TEXT:
                return __('[STORE VIEW]');
        }

        return '';
    }

    /**
     * Check if attribute scope is global.
     *
     * @param EavAttributeInterface $attribute
     * @return bool
     */
    private function isScopeGlobal(EavAttributeInterface $attribute)
    {
        return $attribute->getScope() === PostAttributeInterface::SCOPE_GLOBAL_TEXT;
    }
}
