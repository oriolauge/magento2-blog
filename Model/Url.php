<?php

namespace OAG\Blog\Model;
use OAG\Blog\Api\Data\PostInterface;
use OAG\BlogUrlRewrite\Model\PostUrlPathGenerator;
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

    public function __construct(
        UrlInterface $url,
        PostUrlPathGenerator $postUrlPathGenerator
    )
    {
        $this->url = $url;
        $this->postUrlPathGenerator = $postUrlPathGenerator;
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
}