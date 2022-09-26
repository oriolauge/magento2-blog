<?php

namespace OAG\Blog\Model;
use OAG\Blog\Api\Data\PostInterface;
use OAG\BlogUrlRewrite\Model\PostUrlPathGenerator;
use OAG\BlogUrlRewrite\Model\Config as UrlRewriteConfig;
use Magento\Framework\UrlInterface;

/**
 * Blog url model
 */
class Url
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @param PostUrlPathGenerator
     */
    protected $postUrlPathGenerator;

    /**
     * @var UrlRewriteConfig
     */
    protected $urlRewriteConfig;

    /**
     * Constructor function
     *
     * @param UrlInterface $url
     * @param PostUrlPathGenerator $postUrlPathGenerator
     * @param UrlRewriteConfig $urlRewriteConfig
     */
    public function __construct(
        UrlInterface $url,
        PostUrlPathGenerator $postUrlPathGenerator,
        UrlRewriteConfig $urlRewriteConfig
    )
    {
        $this->url = $url;
        $this->postUrlPathGenerator = $postUrlPathGenerator;
        $this->urlRewriteConfig = $urlRewriteConfig;
    }

    /**
     * Get post url
     *
     * @param Post $post
     * @return void
     */
    public function getPostUrl(PostInterface $post): string
    {
        return $this->url->getUrl('', [ '_direct' => 
            $this->postUrlPathGenerator->getUrlPathWithSuffixAndBlogRoute($post)
        ]);
    }

    /**
     * Get main blog page
     *
     * @param mixed $storeId
     * @return string
     */
    public function getBlogIndexUrl($storeId = null): string
    {
        return $this->url->getUrl(
            $this->urlRewriteConfig->getBlogRoute($storeId)
        );
    }

    /**
     * Get relative main blog page
     *
     * @param mixed $storeId
     * @return string
     */
    public function getBlogIndexRelativeUrl($storeId = null): string
    {
        return $this->urlRewriteConfig->getBlogRoute($storeId) . '/';
    }
}