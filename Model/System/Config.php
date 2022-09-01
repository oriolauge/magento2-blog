<?php

namespace OAG\Blog\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * OAG Blog Config Model
 */
class Config
{
    /**
     * Hold index page blog title config path
     */
    const XML_PATH_INDEX_PAGE_TITLE = 'oag_blog/index_page/title';

    /**
     * Hold index page blog meta title config path
     */
    const XML_PATH_INDEX_PAGE_META_TITLE = 'oag_blog/index_page/meta_title';

    /**
     * Hold index page blog meta keywords config path
     */
    const XML_PATH_INDEX_PAGE_META_KEYWORDS = 'oag_blog/index_page/meta_keywords';

    /**
     * Hold index page blog meta description config path
     */
    const XML_PATH_INDEX_PAGE_META_DESCRIPTION = 'oag_blog/index_page/meta_description';

    /**
     * Hold index page blog display blog summary config path
     */
    const XML_PATH_INDEX_PAGE_DISPLAY_BLOG_SUMMARY = 'oag_blog/index_page/display_blog_summary';

    /**
     * Hold index page blog summary CMS block id config path
     */
    const XML_PATH_INDEX_PAGE_SUMMARY_CMS_BLOCK = 'oag_blog/index_page/summary_cms_block';

    /**
     * Hold topmenu show item config path
     */
    const XML_PATH_TOPMENU_SHOW_ITEM = 'oag_blog/topmenu/show_item';

    /**
     * Hold topmenu item text config path
     */
    const XML_PATH_TOPMENU_ITEM_TEXT = 'oag_blog/topmenu/item_text';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get index page blog title config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getBlogTitle($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_TITLE,
            $storeId
        );
    }

    /**
     * Get index page blog meta title config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getBlogMetaTitle($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_META_TITLE,
            $storeId
        );
    }

    /**
     * Get index page blog meta keywords config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getBlogMetaKeywords($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_META_KEYWORDS,
            $storeId
        );
    }

    /**
     * Get index page blog meta description config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getSummaryCmsBlock($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_SUMMARY_CMS_BLOCK,
            $storeId
        );
    }

    /**
     * Get index page blog meta description config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getBlogMetaDescription($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_META_DESCRIPTION,
            $storeId
        );
    }

    /**
     * Get topmenu item text config value
     *
     * @param mixed $storeId
     * @return void
     */
    public function getBlogTopmenuItemText($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_TOPMENU_ITEM_TEXT,
            $storeId
        );
    }

    /**
     * Get index page blog meta description config value
     *
     * @param mixed $storeId
     * @return bool
     */
    public function canDisplayBlogSummary($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_INDEX_PAGE_DISPLAY_BLOG_SUMMARY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get topmenu show item config value
     *
     * @param mixed $storeId
     * @return bool
     */
    public function canTopmenuShowItem($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TOPMENU_SHOW_ITEM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve store config value
     * @param string $path
     * @param mixed $storeId
     * @return mixed
     */
    protected function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
