<?php
namespace OAG\Blog\Model;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Hreflang model
 */
class Hreflang
{
    /**
     * Holds x-default hreflang tag
     */
    const XDEFAULT = 'x-default';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Url
     */
    protected $url;

    /**
     * Init dependencies
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param Url $url
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        Url $url
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->url = $url;
    }

    /**
     * Get Post hreflang array
     * 
     * return array(lang => url post)
     *
     * @param PostInterface $post
     * @return array
     */
    public function getPostHreflang(PostInterface $post): array
    {
        $statusStoreValues = array_column($post->getAllStatusStoreValues(), 'value', 'store_id');
        $hreflang = [];
        foreach ($this->storeManager->getStores() as $storeId => $storeData) {
            $lang = $this->config->getHreflangCode($storeId);
            $isBlogEnabledInThisStoreView = $this->config->isExtensionEnabled($storeId);
            //If post is enabled in this store
            if (!empty($statusStoreValues[$storeId]) && ($lang) && $isBlogEnabledInThisStoreView) {
                $hreflang[$lang] = $post->getUrlByStoreId($storeId);
            } else if (!isset($statusStoreValues[$storeId])
                && !empty($statusStoreValues[Store::DEFAULT_STORE_ID])
                && ($lang)
                && $isBlogEnabledInThisStoreView) {
                //If we don't have the store value, we will check the default value
                $hreflang[$lang] = $post->getUrlByStoreId($storeId);
            }

            //if user configure more storeviews with default, we only take the first one.
            if (empty($hreflang[self::XDEFAULT]) && $this->config->isHreflangDefaultStoreView($storeId)) {
                $hreflang[self::XDEFAULT] = $post->getUrlByStoreId($storeId);
            }
        }

        return $hreflang;
    }

    /**
     * Get Blog Index Page hreflangs
     * 
     * return array(lang => url post)
     *
     * @return array
     */
    public function getBlogIndexPageHreflang(): array
    {
        $hreflang = [];
        foreach ($this->storeManager->getStores() as $storeId => $storeData) {
            $isBlogEnabledInThisStoreView = $this->config->isExtensionEnabled($storeId);
            $lang = $this->config->getHreflangCode($storeId);

            if ($lang && $isBlogEnabledInThisStoreView) {
                $hreflang[$lang] = $this->url->getBlogIndexUrl($storeId);
            }

            //if user configure more storeviews with default, we only take the first one.
            if (empty($hreflang[self::XDEFAULT]) && $this->config->isHreflangDefaultStoreView($storeId)) {
                $hreflang[self::XDEFAULT] = $this->url->getBlogIndexUrl($storeId);
            }
        }

        return $hreflang;
    }
}