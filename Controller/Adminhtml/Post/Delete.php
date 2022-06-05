<?php
/**
 * delete record UI Form
 */
namespace OAG\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use OAG\Blog\Model\PostFactory;

class Delete extends Action
{

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @param  Context           $context
     * @param  PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory
    ) {
        $this->postFactory = $postFactory;
        parent::__construct($context);
    }

    /**
     * For allow to access or not
     *
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OAG_Blog::post');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);

        try {
            $postData = $this->postFactory->create()->load($id);
            if ($postData->getId()) {
                $postData->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the record.'));
            } else {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*');
    }
}
