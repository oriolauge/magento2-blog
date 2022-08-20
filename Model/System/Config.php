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
    public function getBlogMetaDescription($storeId = null)
    {
        return $this->getConfig(
            self::XML_PATH_INDEX_PAGE_META_DESCRIPTION,
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
