<?php

namespace OAG\Blog\Block\Post\ListItem;
use Magento\Framework\View\Element\Template;

/**
 * Blog posts list toolbar
 */
class Toolbar extends Template
{
    /**
     * Post collection
     *
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $collection = null;

    /**
     * Default block template
     * @var string
     */
    protected $_template = 'post/list/toolbar.phtml';

    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection): Toolbar
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Return post collection instance
     *
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return bool|\Magento\Framework\DataObject|\Magento\Framework\View\Element\AbstractBlock|\Magento\Theme\Block\Html\Pager
     */
    public function getPagerBlock()
    {
        $pagerBlock = $this->getChildBlock($this->getPostToolbarPagerBlockName());
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setLimit(
                $this->getCollection()->getPageSize()
            )->setCollection(
                $this->getCollection()
            );
        } else {
            $pagerBlock = false;
        }

        return $pagerBlock;
    }

    /**
     * Render pagination HTML
     *
     * @return string|null
     */
    public function getPagerHtml(): ?string
    {
        $pagerBlock = $this->getPagerBlock();
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            return $pagerBlock->toHtml();
        }
        return null;
    }
}
