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
     * Hold general enabled config path
     */
    const XML_PATH_GENERAL_ENABLED = 'oag_blog/general/enabled';

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
     * Hold index page blog summary CMS block id config path
     */
    const XML_PATH_INDEX_PAGE_POST_PER_PAGE = 'oag_blog/index_page/posts_per_page';

    /**
     * Hold topmenu show item config path
     */
    const XML_PATH_TOPMENU_SHOW_ITEM = 'oag_blog/topmenu/show_item';

    /**
     * Hold topmenu item text config path
     */
    const XML_PATH_TOPMENU_ITEM_TEXT = 'oag_blog/topmenu/item_text';

    /**
     * Hold Sitemap post enabled
     */
    const XML_PATH_SITEMAP_POST_ENABLED = 'oag_blog/sitemap/post/enabled';

    /**
     * Hold Sitemap post changefreq
     */
    const XML_PATH_SITEMAP_POST_CHANGEFREQ = 'oag_blog/sitemap/post/changefreq';

    /**
     * Hold Sitemap post priority
     */
    const XML_PATH_SITEMAP_POST_PRIORITY = 'oag_blog/sitemap/post/priority';

    /**
     * Hold Sitemap index page enabled
     */
    const XML_PATH_SITEMAP_INDEX_PAGE_ENABLED = 'oag_blog/sitemap/index_page/enabled';

    /**
     * Hold Sitemap index page changefreq
     */
    const XML_PATH_SITEMAP_INDEX_PAGE_CHANGEFREQ = 'oag_blog/sitemap/index_page/changefreq';

    /**
     * Hold Sitemap index page priority
     */
    const XML_PATH_SITEMAP_INDEX_PAGE_PRIORITY = 'oag_blog/sitemap/index_page/priority';

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
     * Check if extension is enabled
     *
     * @param mixed $storeId
     * @return boolean
     */
    public function isExtensionEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
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
     * @return string|null
     */
    public function getSummaryCmsBlock($storeId = null): ?string
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_SUMMARY_CMS_BLOCK,
            $storeId
        );
    }

    /**
     * Get index page blog post per page config value
     *
     * @param mixed $storeId
     * @return int
     */
    public function getPostPerPage($storeId = null): int
    {
        return (int) $this->getConfig(
            self::XML_PATH_INDEX_PAGE_POST_PER_PAGE,
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
     * Check if post sitemap is enabled
     *
     * @param mixed $storeId
     * @return boolean
     */
    public function isSitemapPostEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_POST_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sitemap post change frequency
     *
     * @param mixed $storeId
     * @return string
     */
    public function getSitemapPostChangeFreq($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_SITEMAP_POST_CHANGEFREQ,
            $storeId
        );
    }

    /**
     * Get sitemap post change priority
     *
     * @param mixed $storeId
     * @return string
     */
    public function getSitemapPostPriority($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_SITEMAP_POST_PRIORITY,
            $storeId
        );
    }

    /**
     * Check if index page sitemap is enabled
     *
     * @param mixed $storeId
     * @return boolean
     */
    public function isSitemapIndexPageEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_INDEX_PAGE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sitemap index page change frequency
     *
     * @param mixed $storeId
     * @return string
     */
    public function getSitemapIndexPageChangeFreq($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_SITEMAP_INDEX_PAGE_CHANGEFREQ,
            $storeId
        );
    }

    /**
     * Get sitemap index page change priority
     *
     * @param mixed $storeId
     * @return string
     */
    public function getSitemapIndexPagePriority($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_SITEMAP_INDEX_PAGE_PRIORITY,
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
