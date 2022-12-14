<?php

namespace OAG\Blog\Block\Post;
use Magento\Framework\View\Element\Template;

class ListItem extends Template
{
    /**
     * Get post published at value and convert to show in list page
     *
     * @return string|null
     */
    public function getPublishedAtHtml(): ?string
    {
        if ($this->hasData('post') && $this->getPost()->getPublishedAt()) {
            return '<small>' .
                __('Published at:') .
                ' ' .
                $this->getPost()->getPublishedAt('d/m/Y') .
                '</small>';
        }
        return null;
    }
}
