<?php

namespace OAG\Blog\Model;
use OAG\Blog\Model\Post;
use OAG\Blog\Model\Config;
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
     * @param Config
     */
    protected $config;

    public function __construct(
        UrlInterface $url,
        Config $config
    )
    {
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Get post url
     *
     * @param Post $post
     * @return void
     */
    public function getPostUrl(Post $post): string
    {
        return $this->url->getUrl('', [ '_direct' => 
            $this->config->getBlogRoute()
            . '/'
            . $post->getUrlKey()
            . $this->config->getPostSufix()
        ]);
    }
}