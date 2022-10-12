<?php

namespace OAG\Blog\Model\Sitemap\ItemProvider;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemFactory;
use Magento\Cms\Api\GetBlockByIdentifierInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url;

/**
 * Blog sitemap main page class
 */
class Blog implements ItemProviderInterface
{
    /**
     * @var SitemapItemFactory
     */
    protected $itemFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var GetBlockByIdentifierInterface
     */
    protected $getBlockByIdentifier;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $sitemapItems = [];

    /**
     * Init dependencies
     *
     * @param SitemapItemFactory $itemFactory
     * @param Config $config
     */
    public function __construct(
        SitemapItemFactory $itemFactory,
        Config $config,
        Url $url,
        GetBlockByIdentifierInterface $getBlockByIdentifier,
        LoggerInterface $logger
    ) {
        $this->itemFactory = $itemFactory;
        $this->config = $config;
        $this->url = $url;
        $this->getBlockByIdentifier = $getBlockByIdentifier;
        $this->logger = $logger;
    }

    /**
     * Prepare Main blog page to added in sitemap.xml
     *
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId): array
    {
        if (!$this->config->isExtensionEnabled($storeId)
            || !$this->config->isSitemapIndexPageEnabled($storeId)
        ) {
            return $this->sitemapItems;
        }

        $itemFactoryParams = [
            'url' => $this->url->getBlogIndexRelativeUrl($storeId),
            'priority' => $this->getPriority($storeId),
            'changeFrequency' => $this->getChangeFrequency($storeId)
        ];

        /**
         * We will load Blog index block to add latest date in sitemap.
         * 
         * If we don't have any block configured in backoffice, we won't add the
         * lastmod attribute because is an optional value
         * 
         * @see https://www.sitemaps.org/protocol.html
         */
        if ($this->config->canDisplayBlogSummary()
            && ($blockId = $this->config->getSummaryCmsBlock())
        ) {
            try {
                $block = $this->getBlockByIdentifier->execute($blockId, $storeId);
                $itemFactoryParams['updatedAt'] = $block->getUpdateTime();
            } catch (NoSuchEntityException $e) {
                /**
                 * Silence Exception
                 */
                $this->logger->critical($e->getMessage());
            }
        }

        $this->sitemapItems[] = $this->itemFactory->create(
            $itemFactoryParams
        );

        return $this->sitemapItems;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    private function getChangeFrequency(int $storeId): string
    {
        return $this->config->getSitemapIndexPageChangeFreq($storeId);
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    private function getPriority(int $storeId): string
    {
        return $this->config->getSitemapIndexPagePriority($storeId);
    }
}
