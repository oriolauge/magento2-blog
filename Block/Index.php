<?php

namespace OAG\Blog\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use OAG\Blog\Model\System\Config;

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
     * Construct function
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
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
}
