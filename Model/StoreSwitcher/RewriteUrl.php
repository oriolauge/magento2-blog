<?php
/**
 * @see: Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl;
 */
declare(strict_types=1);

namespace OAG\Blog\Model\StoreSwitcher;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreSwitcherInterface;
use OAG\Blog\Model\UrlFinder;
use OAG\Blog\Model\PostFactory;


/**
 * Handle url rewrites for redirect url
 */
class RewriteUrl implements StoreSwitcherInterface
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RequestFactory
     */
    protected $requestFactory;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var OAG\Blog\Model\UrlFinder
     */
    protected $urlFinder;

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
     * @param Config $config
     * @param UrlFinder $urlFinder
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory,
        UrlFinder $urlFinder,
        PostFactory $postFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->urlFinder = $urlFinder;
        $this->postFactory = $postFactory;
    }

    /**
     * Switch to another store.
     * 
     * based from Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl;
     *
     * @todo Remove this code and use Rewrite url module. Also, you can avoid to load
     * all post model but url_key is not a database index, so is better to improve the
     * code to move all this logic to Rewrite module like catalog product
     * 
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     * @param string $redirectUrl
     * @return string
     */
    public function switch(StoreInterface $fromStore, StoreInterface $targetStore, string $redirectUrl): string
    {
        $targetUrl = $redirectUrl;
        /** @var \Magento\Framework\HTTP\PhpEnvironment\Request $request */
        $request = $this->requestFactory->create(['uri' => $targetUrl]);
        $urlPath = ltrim($request->getPathInfo(), '/');

        if ($targetStore->isUseStoreInUrl()) {
            // Remove store code in redirect url for correct rewrite search
            $storeCode = preg_quote($targetStore->getCode() . '/', '/');
            $pattern = "@^($storeCode)@";
            $urlPath = preg_replace($pattern, '', $urlPath);
        }

        $urlExplode = explode('/', $urlPath);
        if (!$this->urlFinder->isBlogUrl($urlExplode[0])) {
            return $targetUrl;
        }

        //Remove blog part and start to check all blog urls
        unset($urlExplode[0]);

        /**
         * Here, we know that url is a blog url, so
         * we need to find the correct post
         */
        if (count($urlExplode) === 1) {
            $postId = $this->urlFinder->getPostIdByUrlKey(array_pop($urlExplode), $fromStore->getId());
            if ($postId) {
                $postData = $this->postFactory->create();
                $postData->setStoreId($targetStore->getId());
                $postData->load($postId, 'url_key');
                return $postData->getUrl();
            }
        }
        return $targetUrl;
    }
}
