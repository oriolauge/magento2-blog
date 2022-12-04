<?php

namespace OAG\Blog\Block\Post\View;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Opengraph extends Template
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
     * Construct function
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
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
     * Return Open Graph post title
     *
     * @return string
     */
    public function getOpenGraphTitle(): string
    {
        return $this->getData(self::POST_FIELD)->getOpenGraphTitle();
    }

    /**
     * Get Open Graph post description
     *
     * @return string|null
     */
    public function getOpenGraphDescription(): ?string
    {
        return $this->getData(self::POST_FIELD)->getOpenGraphDescription();
    }

    /**
     * Get Open Graph post Image
     *
     * @return string
     */
    public function getOpenGraphImageUrl(): string
    {
        return $this->getData(self::POST_FIELD)->getOpenGraphImageAbsoluteUrl();
    }

    /**
     * Get post url
     *
     * @return string
     */
    public function getPostUrl(): string
    {
        return $this->getData(self::POST_FIELD)->getUrl();
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
}
