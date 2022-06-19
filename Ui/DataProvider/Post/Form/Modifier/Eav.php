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
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

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

    public function __construct(
        CollectionFactory $collection,
        RequestInterface $request,
        PostRepositoryInterface $postRepository,
        AttributeCollectionFactory $attributeCollectionFactory = null
    )
    {
        $this->collection = $collection->create();
        $this->request = $request;
        $this->postRepository = $postRepository;
        $this->attributeCollectionFactory = $attributeCollectionFactory
            ?: ObjectManager::getInstance()->get(AttributeCollectionFactory::class);
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $attributes = !empty($this->getAttributes()) ? $this->getAttributes() : [];
        if ($attributes) {
            $meta['general']['children'] = $this->getAttributesMeta($attributes, 'general');
            $meta['general']['arguments']['data']['config']['componentType'] = Fieldset::NAME;
            $meta['general']['arguments']['data']['config']['dataScope'] = 'data.post';
            $meta['general']['arguments']['data']['config']['label'] = __("Main Information");
            $meta['general']['arguments']['data']['config']['collapsible'] = false;
            $meta['general']['arguments']['data']['config']['sortOrder'] = 10;
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
            foreach($attribute as $key => $testValue) {
                //$meta[$testValue->getAttributeCode()]['arguments']['data']['config']['service']['template'] = 'ui/form/element/helper/service';
                //$meta[$testValue->getAttributeCode()]['arguments']['data']['config']['disabled'] = 1;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['componentType'] = Field::NAME;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['dataType'] = $testValue->getFrontendInput();
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['label'] = $testValue->getFrontendLabel();
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['formElement'] = 'input';
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['source'] = $groupCode;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['visible'] = true;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['required'] = $testValue->getIsRequired();
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['notice'] = null;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['default'] = null;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['code'] = $testValue->getAttributeCode();
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['dataScope'] = $testValue->getAttributeCode();
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['sortOrder'] = $order;
                $order++;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['validation'] = ['required-entry' => true];
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['globalScope'] = false;
                $meta[$testValue->getAttributeCode()]['arguments']['data']['config']['scopeLabel'] = __('[store view]');

            }
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
     * @return ProductAttributeInterface[]
     */
    protected function getAttributes()
    {
        if (!$this->attributes) {
            $collection = $this->attributeCollectionFactory->create();
            $post = $this->getCurrentPost();
            $collection->setAttributeSetFilter($post->getAttributeSetId());
            foreach ($collection->getItems() as $attribute) {
                $this->attributes[$attribute->getAttributeGroupId()][] = $attribute;
            }
        }

        return $this->attributes;
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
