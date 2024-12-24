<?php

namespace OAG\Blog\Model;
use OAG\Blog\Api\Data\PostInterface;
use OAG\BlogUrlRewrite\Model\PostUrlPathGenerator;
use OAG\BlogUrlRewrite\Model\MainBlogUrlPathGenerator as MainBlogUrlPathGenerator;
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
     * @var MainBlogUrlPathGenerator
     */
    protected $mainBlogUrlPathGenerator;

    /**
     * Constructor function
     *
     * @param UrlInterface $url
     * @param PostUrlPathGenerator $postUrlPathGenerator
     * @param MainBlogUrlPathGenerator $mainBlogUrlPathGenerator
     */
    public function __construct(
        UrlInterface $url,
        PostUrlPathGenerator $postUrlPathGenerator,
        MainBlogUrlPathGenerator $mainBlogUrlPathGenerator
    )
    {
        $this->url = $url;
        $this->postUrlPathGenerator = $postUrlPathGenerator;
        $this->mainBlogUrlPathGenerator = $mainBlogUrlPathGenerator;
    }

    /**
     * Get post url
     *
     * We add _scope param to return base url from differents storeview.
     * This feature is used to generate hreflangs.
     *
     * We generate the url manually and not use getDirectUrl because we need to
     * change the "scope" (store_id) to generate different urls in one request for hreflang.
     * If you use getDirectUrl, you need to change scope (setScope function) and you cause
     * differents bugs because last scope is cached and others urls in website will generated
     * in different scope like current one.
     *
     * You can check Magento\Framework\Url->getBaseUrl and getRouteUrl to see scope
     * logic, also, getUrl function added a final / that we don't want
     *
     * @param PostInterface $post
     * @param mixed $storeId
     * @return string
     */
    public function getPostUrl(PostInterface $post, $storeId = null): string
    {
        $params = null;
        if (is_numeric($storeId)) {
            $params = [
                '_scope' => $storeId
            ];
        }
        return $this->url->getBaseUrl($params) . $this->getPostRelativeUrl($post, $storeId);
    }

    /**
     * Return relative post url.
     * 
     * Currently we don't add the language code.
     *
     * @param PostInterface $post
     * @param mixed $storeId
     * @return string
     */
    public function getPostRelativeUrl(PostInterface $post, $storeId = null): string
    {
        return $this->postUrlPathGenerator->getUrlPathWithSuffixAndBlogRoute($post, $storeId);
    }

    /**
     * Get absolute main blog page from current store view.
     *
     * @return string
     */
    public function getBlogIndexUrl(): string
    {
        return $this->url->getDirectUrl($this->getBlogIndexRelativeUrl());
    }

    /**
     * Get relative main blog page
     *
     * Currently we don't add the language code.
     *
     * @param mixed $storeId
     * @return string
     */
    public function getBlogIndexRelativeUrl($storeId = null): string
    {
        return $this->mainBlogUrlPathGenerator->getMainBlogUrlPathWithSuffix($storeId);
    }
}
