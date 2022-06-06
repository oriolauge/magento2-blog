<?php
/**
 * @see: Magento\UrlRewrite\Model\StoreSwitcher\RewriteUrl;
 */
declare(strict_types=1);

namespace OAG\Blog\Model\StoreSwitcher;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreSwitcherInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Handle url rewrites for redirect url
 */
class RewriteUrl implements StoreSwitcherInterface
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RequestFactory
     */
    private $requestFactory;

    /**
     * @param UrlFinderInterface $urlFinder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RequestFactory $requestFactory
    ) {
        $this->requestFactory = $requestFactory;
    }

    /**
     * Switch to another store.
     *
     * @param StoreInterface $fromStore
     * @param StoreInterface $targetStore
     * @param string $redirectUrl
     * @return string
     */
    public function switch(StoreInterface $fromStore, StoreInterface $targetStore, string $redirectUrl): string
    {
        if (strpos($redirectUrl, 'blog') !== false) {
        //var_dump($redirectUrl); die();
            return 'https://dev.rittagraf.com/test';
        }
        return $redirectUrl;
    }
}
