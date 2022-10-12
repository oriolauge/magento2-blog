<?php

namespace OAG\Blog\Model\Sitemap\ItemProvider;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemFactory;
use OAG\Blog\Model\Post\ListCollection;
use OAG\Blog\Model\System\Config;

/**
 * Post sitemap class
 */
class Post implements ItemProviderInterface
{
    /**
     * @var SitemapItemFactory
     */
    protected $itemFactory;

    /**
     * @var ListCollection
     */
    protected $listCollection;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $sitemapItems = [];

    /**
     * Init dependencies
     *
     * @param SitemapItemFactory $itemFactory
     * @param ListCollection $listCollection
     * @param Config $config
     */
    public function __construct(
        SitemapItemFactory $itemFactory,
        ListCollection $listCollection,
        Config $config
    ) {
        $this->itemFactory = $itemFactory;
        $this->listCollection = $listCollection;
        $this->config = $config;
    }

    /**
     * Prepare Post blog pages to added in sitemap.xml
     * 
     * @todo add main post images in sitemap
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItems($storeId): array
    {
        if (!$this->config->isExtensionEnabled($storeId)
            || !$this->config->isSitemapPostEnabled($storeId)
        ) {
            return $this->sitemapItems;
        }
        
        $postListCollection = $this->listCollection->getPostListCollection();
        $postChangeFreq = $this->getChangeFrequency($storeId);
        $postPriority = $this->getPriority($storeId);
        foreach ($postListCollection as $post) {
            $this->sitemapItems[] = $this->itemFactory->create(
                [
                    'url' => $post->getRelativeUrl(),
                    'updatedAt' => $post->getUpdatedAt(),
                    'priority' => $postPriority,
                    'changeFrequency' => $postChangeFreq
                ]
            );
        }

        return $this->sitemapItems;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    private function getChangeFrequency(int $storeId): string
    {
        return $this->config->getSitemapPostChangeFreq($storeId);
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    private function getPriority(int $storeId): string
    {
        return $this->config->getSitemapPostPriority($storeId);
    }
}
