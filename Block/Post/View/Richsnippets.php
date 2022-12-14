<?php

namespace OAG\Blog\Block\Post\View;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use OAG\Blog\Model\Url;

class Richsnippets extends Template
{
    /**
     * Holds Post field key value
     */
    const POST_FIELD = 'post';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Url
     */
    protected $url;

    /**
     * Init dependencies
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Url $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->hasData(self::POST_FIELD)) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get store name
     *
     * @return string|null
     */
    public function getSiteName(): ?string
    {
        return $this->scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get blog posting rich snippets json to print in html
     *
     * @return string
     */
    public function getBlogPostingRichSnippetsJson()
    {
        return json_encode($this->getBlogPostingRichSnippets());
    }

    /**
     * Generate blog posting rich snippets
     *
     * @return array
     */
    protected function getBlogPostingRichSnippets(): array
    {
        $siteName = $this->getSiteName();
        $blogIndexUrl = $this->url->getBlogIndexUrl();
        $blogPostingRichSnippets = [
            '@context' => 'http://schema.org',
            '@type' => 'BlogPosting',
            '@id' => $this->getData(self::POST_FIELD)->getUrl(),
            'mainEntityOfPage' => $blogIndexUrl,
            'description' => $this->stripTags($this->getData(self::POST_FIELD)->getShortContent()),
            'headline' => $this->getData(self::POST_FIELD)->getTitle(),
            'name' => $this->getData(self::POST_FIELD)->getTitle(),
            'url' => $this->getData(self::POST_FIELD)->getUrl(),
            'datePublished' => $this->getData(self::POST_FIELD)->getPublishedAt('Y-m-d'),
            'dateModified' => $this->getData(self::POST_FIELD)->getUpdatedAt('Y-m-d'),
            'author' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $blogIndexUrl
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
            ]
        ];

        if ($mainImage = $this->getData(self::POST_FIELD)->getImageUrl()) {
            $blogPostingRichSnippets['image'] = $mainImage;
        }

        return $blogPostingRichSnippets;
    }
}
