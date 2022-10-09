<?php
/**
 * Preview admin action
 */
namespace OAG\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use OAG\Blog\Api\PostRepositoryInterface;
use Magento\Store\Model\Store;
use OAG\Blog\Api\Data\PostInterface;

class Preview extends Action
{
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository
    ) {
        $this->postRepository = $postRepository;
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
     * Preview action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id', null);
        if (!is_numeric($id)) {
            $this->messageManager->addErrorMessage(
                __('Post ID %1 is not a valid parameter', $id)
            );
            $resultRedirect->setPath('*/*');
        }

        try {
            $storeId = $this->getRequest()->getParam('store_id', Store::DEFAULT_STORE_ID);
            $post = $this->postRepository->getById($id, $storeId);
            $postUrl = $post->getUrl();
            if (!$post->getStatus() && $post->getPreviewHash()) {
                $postUrl .= (false === strpos($postUrl, '?')) ? '?' : '&';
                $postUrl .= PostInterface::KEY_PREVIEW_HASH . '=' . $post->getPreviewHash();
            }
            $resultRedirect->setPath($postUrl);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception);
            $resultRedirect->setPath('*/*');
        }

        return $resultRedirect;
    }
}
