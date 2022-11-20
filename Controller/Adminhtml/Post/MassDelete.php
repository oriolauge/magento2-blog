<?php
/**
 * mass delete records 
 */
namespace OAG\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use OAG\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class MassDelete
 */
class MassDelete extends Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Collection
     */
    protected $postCollection;

    /**
     * @param  Context    $context
     * @param  Filter     $filter
     * @param  Collection $postCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Collection $postCollection
    ) {
        $this->filter = $filter;
        $this->postCollection = $postCollection;
        parent::__construct($context);
    }

    /**
     * For allow to access or not
     *
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OAG_Blog::post_delete');
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException | \Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->postCollection);
        $collectionSize = $collection->getSize();
        $collection->walk('delete');

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
