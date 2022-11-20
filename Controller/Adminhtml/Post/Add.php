<?php
/**
 * file for new record UI Form
 */
namespace OAG\Blog\Controller\Adminhtml\Post;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Add extends Action
{
    /**
     * For allow to access or not
     *
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OAG_Blog::post_create');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Add New Record'));
        return $resultPage;
    }
}