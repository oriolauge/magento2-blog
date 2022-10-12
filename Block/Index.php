<?php

namespace OAG\Blog\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\Post\ListCollection;
use OAG\Blog\Model\ResourceModel\Post\Collection;

/**
 * Main blog page
 */
class Index extends Template
{
    const KEY_SUMMARY_CMS_BLOCK_HTML = 'summary_cms_block_html';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ListCollection
     */
    protected $postListCollection;

    /**
     * @var Collection
     */
    private $postCollection = false;

    /**
     * Construct function
     *
     * @param Context $context
     * @param Config $config
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ListCollection $postListCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        ListCollection $postListCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->postListCollection = $postListCollection;
    }

    /**
     * Return Summary CMS block html
     *
     * @return string|null
     */
    public function getSummaryCmsBlockHtml(): ?string
    {
        if (!$this->config->canDisplayBlogSummary()) {
            return null;
        }

        $blockId = $this->config->getSummaryCmsBlock();
        if (!$blockId) {
            return null;
        }

        if (!$this->getData(self::KEY_SUMMARY_CMS_BLOCK_HTML)) {
            $html = $this->getLayout()->createBlock(
                \Magento\Cms\Block\BlockByIdentifier::class
            )->setIdentifier(
                $blockId
            )->toHtml();
            $this->setData(self::KEY_SUMMARY_CMS_BLOCK_HTML, $html);
        }
        return $this->getData(self::KEY_SUMMARY_CMS_BLOCK_HTML);
    }

    /**
     * Get post Collection to show in list.phtml
     *
     * @return Collection
     */
    public function getPostCollection(): Collection
    {
        if (!$this->postCollection) {
            $page = (int) $this->getRequest()->getParam('p') ?: 1;
            $pageSize = $this->config->getPostPerPage();
            $this->postCollection = $this->postListCollection->getPostListCollection($pageSize, $page);
        }
        return $this->postCollection;
    }

    /**
     * Get post item Html
     *
     * @param PostInterface $post
     * @return string|null
     */
    public function getPostHtml(PostInterface $post): ?string
    {
        return $this->getChildBlock($this->getPostListItemBlockName())->setPost($post)->toHtml();
    }

    /**
     * Retrieve Toolbar Html
     *
     * @return string|null
     */
    public function getToolbarHtml(): ?string
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            return $this->getChildHtml($blockName);
        }
        return null;
    }

    /**
     * Before block to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getChildBlock($blockName);
            if ($block) {
                $block->setCollection($this->getPostCollection());
            }
        }

        return parent::_beforeToHtml();
    }
}
