<?php

namespace OAG\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ListItem extends Template
{
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
                $this->timezone->date(new \DateTime($this->getPost()->getPublishedAt()))->format('d/m/Y') .
                '</small>';
        }
        return null;
    }

}