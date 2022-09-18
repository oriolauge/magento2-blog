<?php

namespace OAG\Blog\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Api\Data\PostSearchResultsInterface;
use OAG\Blog\Api\Data\PostInterface;

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
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Construct function
     *
     * @param Context $context
     * @param Config $config
     * @param PostRepositoryInterface $postRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        PostRepositoryInterface $postRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->postRepository = $postRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
                \Magento\Cms\Block\Block::class
            )->setBlockId(
                $blockId
            )->toHtml();
            $this->setData(self::KEY_SUMMARY_CMS_BLOCK_HTML, $html);
        }
        return $this->getData(self::KEY_SUMMARY_CMS_BLOCK_HTML);
    }

    /**
     * Get post search result to show in list.phtml
     *
     * @return PostSearchResultsInterface
     */
    public function getPostSearchResults(): PostSearchResultsInterface
    {
        $postSearchResults = $this->postRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(PostInterface::KEY_STATUS, Boolean::VALUE_YES)
                ->create()
        );

        return $postSearchResults;
    }

    public function getPostHtml(PostInterface $post): ?string
    {
        return $this->getChildBlock('oagblog_post_list_item')->setPost($post)->toHtml();
    }
}
