<?php

namespace OAG\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;

class View extends Template implements IdentityInterface
{
    const POST_FIELD = 'post';

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
     * Return Main image post alt.
     * If image post alt is empty, we will return post title
     *
     * @return string|null
     */
    public function getMainImageText(): ?string
    {
        $text = $this->_getData(self::POST_FIELD)->getImageAlt();
        return $text ?? $this->_getData(self::POST_FIELD)->getTitle();
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
        return $this->_getData(self::POST_FIELD)->getPublishedAt('d/m/Y');
    }

    /**
     * Return identifiers for post content
     *
     * This is necessary because when post object change content, his identities tag will be invalidate.
     * With this function, we will invalidate block content too and user will see latest changes.
     *
     * Also, is necessary to add new interface in this block
     *
     * @return array
     */
    public function getIdentities()
    {
        if ($this->hasData(self::POST_FIELD)) {
            return $this->getData(self::POST_FIELD)->getIdentities();
        }
        return [];
    }
}