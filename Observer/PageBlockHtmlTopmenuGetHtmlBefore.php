<?php

namespace OAG\Blog\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use OAG\Blog\Model\Topmenu;

/**
 * Blog page block html topmenu get html observer
 */
class PageBlockHtmlTopmenuGetHtmlBefore implements ObserverInterface
{
    /**
     * @var Topmenu
     */
    protected $topmenu;

    /**
     * Construct function
     *
     * @param Topmenu $topmenu
     */
    public function __construct(
        Topmenu $topmenu
    ) {
        $this->topmenu = $topmenu;
    }

    /**
     * Page block html topmenu gethtml before
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $request = $observer->getRequest();

        if ($addedNodes = $this->topmenu->getBlogNode($menu, $request, $tree)) {
            $menu->addChild($addedNodes);
        }
    }
}
