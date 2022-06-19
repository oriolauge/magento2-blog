<?php
/**
 * Main post model
 */
namespace OAG\Blog\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use OAG\Blog\Model\Url;
use OAG\Blog\Api\Data\PostInterface;

class Post extends AbstractModel implements IdentityInterface, PostInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'oag_blog_post';

    /**
     * @var string
     */
    protected $_cacheTag = 'oag_blog_post';

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'oag_blog_post';

    public function __construct(
        Context $context,
        Registry $registry,
        Url $url,
        StoreManagerInterface $storeManager
    )
    {
        $this->url = $url;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\OAG\Blog\Model\ResourceModel\Post::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Save from collection data
     *
     * @param array $data
     * @return $this|bool
     */
    public function saveCollection(array $data)
    {
        if (isset($data[$this->getId()])) {
            $this->addData($data[$this->getId()]);
            $this->getResource()->save($this);
        }
        return $this;
    }

    /**
     * Set attribute set entity type id
     *
     * @param int $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        return $this->setData(self::KEY_ENTITY_TYPE_ID, $entityTypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTypeId()
    {
        return $this->getData(self::KEY_ENTITY_TYPE_ID);
    }

    /**
     * Set attribute set id
     *
     * @param int $attrSetId
     * @return $this
     */
    public function setAttributeSetId($attrSetId)
    {
        return $this->setData(self::KEY_ATTR_TYPE_ID, $attrSetId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::KEY_ATTR_TYPE_ID);
    }

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    public function getUrl()
    {
        return $this->url->getPostUrl($this);
    }

    /**
     * Get Main Title
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    public function getMainTitle()
    {
        return $this->_getData(self::KEY_MAIN_TITLE);
    }

    /**
     * set Main Title
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    public function setMainTitle($mainTitle)
    {
        return $this->setData(self::KEY_MAIN_TITLE, $mainTitle);
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData(self::KEY_STORE_ID)) {
            return (int) $this->getData(self::KEY_STORE_ID);
        }
        return (int) $this->storeManager->getStore()->getId();
    }
}
