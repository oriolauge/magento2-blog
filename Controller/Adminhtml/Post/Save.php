<?php
/**
 * save records
 */
namespace OAG\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use OAG\Blog\Model\PostFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Save extends Action
{
    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param PostFactory $postFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->postFactory = $postFactory;
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * For allow to access or not
     *
     * return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OAG_Blog::post');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $params = [];
            $postData = $this->postFactory->create();
            if (!empty($data['store_id'])) {
                $storeId = (int) $data['store_id'];
                $params['store'] = $storeId;
            } else {
                $storeId = Store::DEFAULT_STORE_ID;
            }

            $store = $this->storeManager->getStore($storeId);
            $this->storeManager->setCurrentStore($store->getCode());

            $postData->setStoreId($storeId);
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
                /**
                 * This line is necessary to load all attributes and can call differents events
                 * like beforeSave, afterSave, etc.
                 *
                 * See Magento\Eav\Model\Entity\AbstractEntity::walkAttributes(), in getAttributesByScope()
                 * foreach, here we need the attributes loaded. If not, we won't call the events exposed before
                 */
                $postData->getResource()->loadAllAttributes();
                $postData->setAttributeSetId($postData->getDefaultAttributeSetId());
            } else {
                $postData->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
            }

            $postData->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if (!empty($data['use_default'])) {
                foreach ($data['use_default'] as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $postData->setData($attributeCode, null);
                    }
                }
            }

            $this->_eventManager->dispatch(
                'oag_blog_post_prepare_save',
                ['object' => $postData, 'request' => $this->getRequest()]
            );

            try {
                $postData->save();
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $postData->getId();
                    $params['_current'] = true;
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', $params);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
