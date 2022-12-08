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
     * We add store_id param to avoid chached result if we want to get all urls from all stores
     * This feature is used to generate hreflangs
     *
     * You can check Magento\Framework\Url->getUrl function and see cached logic
     *
     * @param PostInterface $post
     * @param mixed $storeId
     * @return string
     */
    public function getPostUrl(PostInterface $post, $storeId = null): string
    {
        $params = [];
        if (is_numeric($storeId)) {
            $this->url->setScope($storeId);
            $params['store_id'] = $storeId;
        }
        return $this->url->getDirectUrl(
            $this->postUrlPathGenerator->getUrlPathWithSuffixAndBlogRoute($post, $storeId),
            $params
        );
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
     * Get main blog page
     *
     * We add store_id param to avoid chached result if we want to get all urls from all stores
     * This feature is used to generate hreflangs
     *
     * You can check Magento\Framework\Url->getUrl function and see cached logic
     *
     * @param mixed $storeId
     * @return string
     */
    public function getBlogIndexUrl($storeId = null): string
    {
        $params = [];
        if (is_numeric($storeId)) {
            $this->url->setScope($storeId);
            $params['store_id'] = $storeId;
        }
        return $this->url->getDirectUrl(
            $this->mainBlogUrlPathGenerator->getMainBlogUrlPathWithSuffix($storeId),
            $params
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
        return $this->mainBlogUrlPathGenerator->getMainBlogUrlPathWithSuffix($storeId);
    }
}
