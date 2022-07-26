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
use Magento\Widget\Model\Template\Filter;
use Magento\Store\Model\Store;

class Post extends AbstractModel implements IdentityInterface, PostInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'oag_blog_post';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Filter
     */
    protected $templateFilter;

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
        StoreManagerInterface $storeManager,
        Filter $templateFilter
    )
    {
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->templateFilter = $templateFilter;
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
        return $this->_getData(self::KEY_ATTR_TYPE_ID);
    }

    /**
     * {@inheritdoc}
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
     * @inheritDoc
     *
     * @return void
     */
    public function getUrlKey()
    {
        return $this->_getData(self::KEY_URL_KEY);
    }

    /**
     * Get Title
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    public function getTitle()
    {
        return $this->_getData(self::KEY_TITLE);
    }

    /**
     * Set title
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    public function setTitle($title)
    {
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData(self::KEY_STORE_ID)) {
            return (int) $this->_getData(self::KEY_STORE_ID);
        }
        return (int) $this->storeManager->getStore()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($processHtml = true)
    {
        $content = $this->_getData(self::KEY_CONTENT);
        if ($processHtml === true) {
            $content = $this->templateFilter->filter($content);
        }

        return $content;
    }

    /**
     * @inheritDoc
     *
     * @todo: improve this function and get option to configure stores/websites
     * to show the blog post
     * @return array
     */
    public function getStores()
    {
        return [Store::DEFAULT_STORE_ID];
    }
}
