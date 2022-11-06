<?php

namespace OAG\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class View extends Template
{
    const POST_FIELD = 'post';

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Construct function
     *
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->timezone = $timezone;
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
     * Return main post image
     *
     * @return string|null
     */
    public function getMainImage(): ?string
    {
        return $this->_getData(self::POST_FIELD)->getImageUrl();
    }

    /**
     * Return Main image post alt
     *
     * @return string|null
     */
    public function getMainImageAlt(): ?string
    {
        return $this->_getData(self::POST_FIELD)->getImageAlt();
    }

    /**
     * Return post content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->_getData(self::POST_FIELD)->getContent();
    }

    /**
     * Get post published at value and convert to show in list page
     *
     * @return string|null
     */
    public function getPublishedAt(): ?string
    {
        return $this->timezone->date(new \DateTime($this->_getData(self::POST_FIELD)->getPublishedAt()))->format('d/m/Y');
    }
}