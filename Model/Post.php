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
use Magento\Widget\Model\Template\Filter;
use OAG\Blog\Model\Url;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\Post\Image;
use OAG\Blog\Model\Post\GetNextAndPrevious;
use OAG\Blog\Block\Index;
use OAG\Blog\Api\PostAttributeRepositoryInterface;

class Post extends AbstractModel implements IdentityInterface, PostInterface
{
    /**
     * Holds cache tag
     * This constant value means OAG (oag) Blog (b) Post (p) and we prefere a short name
     * like product or categories CACHE_TAGs
     */
    const CACHE_TAG = 'oag_b_p';

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
     * @var Image
     */
    protected $image;

    /**
     * @var GetNextAndPrevious
     */
    protected $getNextAndPrevious;

    /**
     * @var PostInterface|null|false
     */
    protected $nextPost = false;

    /**
     * @var PostInterface|null|false
     */
    protected $previousPost = false;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'oag_blog_post';

    /**
     * @var PostAttributeRepositoryInterface
     */
    protected $postAttributeRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        Url $url,
        StoreManagerInterface $storeManager,
        Filter $templateFilter,
        Image $image,
        GetNextAndPrevious $getNextAndPrevious,
        PostAttributeRepositoryInterface $postAttributeRepository
    )
    {
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->templateFilter = $templateFilter;
        $this->image = $image;
        $this->getNextAndPrevious = $getNextAndPrevious;
        $this->postAttributeRepository = $postAttributeRepository;
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
     * We added KEY_OAG_BLOG_POST_INDEX_CACHE_TAG because in every save process, we will
     * invalidate the blog listing view and we will see new blog posts
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [Index::KEY_OAG_BLOG_POST_INDEX_CACHE_TAG];
        if ($this->getId()) {
            $identities[] = self::CACHE_TAG . '_' . $this->getId();
        }
        return $identities;
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

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        $storeId = is_numeric($this->getStoreId()) ? $this->getStoreId() : null;
        return $this->getUrlByStoreId($storeId);
    }

    /**
     * @inheritDoc
     */
    public function getUrlByStoreId($storeId)
    {
        return $this->url->getPostUrl($this, $storeId);
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
     * @inheritDoc
     */
    public function getRelativeUrl()
    {
        $storeId = is_numeric($this->getStoreId()) ? $this->getStoreId() : null;
        return $this->url->getPostRelativeUrl($this, $storeId);
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
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        $metaTitle = $this->_getData(self::KEY_META_TITLE);
        return $metaTitle ?: $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords()
    {
        return $this->_getData(self::KEY_META_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->_getData(self::KEY_META_DESCRIPTION);
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
     * @inheritDoc
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
     */
    public function getImageUrl()
    {
        if ($this->hasData(self::KEY_IMAGE)) {
            return $this->image->getUrl($this);
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getImageAlt()
    {
        if ($this->hasData(self::KEY_IMAGE_ALT)) {
            return $this->_getData(self::KEY_IMAGE_ALT);
        }
        return $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getListImageUrl()
    {
        if ($this->hasData(self::KEY_LIST_IMAGE)) {
            return $this->image->getUrl($this, self::KEY_LIST_IMAGE);
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getListImageAlt()
    {
        if ($this->hasData(self::KEY_LIST_IMAGE_ALT)) {
            return $this->_getData(self::KEY_LIST_IMAGE_ALT);
        }
        return $this->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getShortContent()
    {
        return $this->_getData(self::KEY_SHORT_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getPublishedAt($dateFormat = null)
    {
        return $this->_getData(self::KEY_PUBLISHED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewHash()
    {
        return $this->_getData(self::KEY_PREVIEW_HASH);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->_getData(self::KEY_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function getPreviousPost()
    {
        if ($this->previousPost === false) {
            $this->previousPost = $this->getNextAndPrevious->getPreviousPost($this);
        }

        return $this->previousPost;
    }

    /**
     * @inheritDoc
     */
    public function getNextPost()
    {
        if ($this->nextPost === false) {
            $this->nextPost = $this->getNextAndPrevious->getNextPost($this);
        }

        return $this->nextPost;
    }

    /**
     * @inheritDoc
     */
    public function getOpenGraphTitle()
    {
        $openGraphTitle = $this->_getData(self::KEY_OPEN_GRAPH_TITLE);
        return $openGraphTitle ?: $this->getMetaTitle();
    }

    /**
     * @inheritDoc
     */
    public function getOpenGraphDescription()
    {
        $openGraphDescription = $this->_getData(self::KEY_OPEN_GRAPH_DESCRIPTION);
        return $openGraphDescription ?: $this->getMetaDescription();
    }

    /**
     * @inheritDoc
     */
    public function getOpenGraphImageAbsoluteUrl()
    {
        if ($this->hasData(self::KEY_OPEN_GRAPH_IMAGE)) {
            return $this->image->getUrl($this, self::KEY_OPEN_GRAPH_IMAGE);
        }
        return $this->getImageUrl();
    }

    /**
     * @inheritDoc
     */
    public function getAllStatusStoreValues()
    {
        $statusAttribute = $this->postAttributeRepository->get(self::KEY_STATUS);
        return $this->_getResource()->getAllStoreValues($this, $statusAttribute);
    }
}
