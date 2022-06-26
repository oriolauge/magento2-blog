<?php
/**
 * Resource model Post
 */
namespace OAG\Blog\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use OAG\Blog\Setup\PostSetup;

class Post extends AbstractEntity
{
    /**
     * Store id
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(PostSetup::ENTITY_TYPE_CODE);
        $this->setConnection(PostSetup::ENTITY_TYPE_CODE . '_read', PostSetup::ENTITY_TYPE_CODE . '_write');
        $this->_storeManager = $storeManager;
    }

    /**
     * Set attribute set id and entity type id value
     * 
     * @todo: move this function to other place because when we change the save logic to
     * getEntityManager()->save, this function seems is not used anymore
     *
     * @param \Magento\Framework\DataObject $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _beforeSave(\Magento\Framework\DataObject $object)
    {
        $object->setAttributeSetId($object->getAttributeSetId() ?: $this->getEntityType()->getDefaultAttributeSetId());
        return parent::_beforeSave($object);
    }

    /**
     * Save entity's attributes into the object's resource
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getEntityManager()->save($object);
        return $this;
    }

    /**
     * Returns EntityManager object
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\EntityManager::class);
        }
        return $this->entityManager;
    }

    /**
     * Return Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(PostSetup::ENTITY_TYPE_CODE);
        }
        return parent::getEntityType();
    }

    /**
     * Retrieve Post entity default attributes
     *
     * @return string[]
     */
    protected function _getDefaultAttributes()
    {
        return [
            'attribute_set_id',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Set store Id
     *
     * @param integer $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Returns default Store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Store::DEFAULT_STORE_ID;
    }

    /**
     * Set Attribute values to be saved
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param mixed $value
     * @return $this
     */
    protected function _saveAttribute($object, $attribute, $value)
    {
        $table = $attribute->getBackend()->getTable();
        if (!isset($this->_attributeValuesToSave[$table])) {
            $this->_attributeValuesToSave[$table] = [];
        }

        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $storeId = $object->getStoreId() ?: $this->getDefaultStoreId();
        $data = [
            $entityIdField => $object->getId(),
            'attribute_id' => $attribute->getId(),
            'value' => $this->_prepareValueForSave($value, $attribute),
            'store_id' => $storeId,
        ];

        if ($attribute->isScopeStore()) {
            /**
             * Update attribute value for store
             */
            $this->_attributeValuesToSave[$table][] = $data;
        } elseif ($attribute->isScopeWebsite() && $storeId != $this->getDefaultStoreId()) {
            /**
             * Update attribute value for website
             */
            $storeIds = $this->_storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            foreach ($storeIds as $storeId) {
                $data['store_id'] = (int) $storeId;
                $this->_attributeValuesToSave[$table][] = $data;
            }
        } else {
            /**
             * Update global attribute value
             */
            $data['store_id'] = $this->getDefaultStoreId();
            $this->_attributeValuesToSave[$table][] = $data;
        }

        return $this;
    }

    /**
     * Retrieve select object for loading entity attributes values
     *
     * Join attribute store value
     *
     * @see \Magento\Catalog\Model\ResourceModel\AbstractResource::_getLoadAttributesSelect()
     * @param \Magento\Framework\DataObject $object
     * @param string $table
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        /**
         * This condition is applicable for all cases when we was work in not single
         * store mode, customize some value per specific store view and than back
         * to single store mode. We should load correct values
         */
        if ($this->_storeManager->hasSingleStore()) {
            $storeId = (int) $this->_storeManager->getStore(true)->getId();
        } else {
            $storeId = (int) $object->getStoreId();
        }

        $setId = $object->getAttributeSetId();
        $storeIds = [$this->getDefaultStoreId()];
        if ($storeId != $this->getDefaultStoreId()) {
            $storeIds[] = $storeId;
        }

        $select = $this->getConnection()
            ->select()
            ->from(['attr_table' => $table], [])
            ->where("attr_table.{$this->getLinkField()} = ?", $object->getData($this->getLinkField()))
            ->where('attr_table.store_id IN (?)', $storeIds, \Zend_Db::INT_TYPE);

        if ($setId) {
            $select->join(
                ['set_table' => $this->getTable('eav_entity_attribute')],
                $this->getConnection()->quoteInto(
                    'attr_table.attribute_id = set_table.attribute_id' . ' AND set_table.attribute_set_id = ?',
                    $setId
                ),
                []
            );
        }
        return $select;
    }
}
