<?php
/**
 * Delete records button in UI Form
 */

namespace OAG\Blog\Block\Adminhtml\Post\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param Context  $context
     */
    public function __construct(
        Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
        $this->authorization = $context->getAuthorization();
    }

    /**
     * Delete records confirm popup
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->authorization->isAllowed('OAG_Blog::post_delete') &&
            $this->request->getParam('entity_id') > 0) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'id' => 'post-edit-delete-button',
                'data_attribute' => [
                    'url' => $this->getDeleteUrl(),
                ],
                'on_click' =>
                'deleteConfirm(\'' . __("Are you sure you want to do this?") . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;

    }

    /**
     * Delete records controller URL
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['entity_id' => (int) $this->request->getParam('entity_id')]);
    }
}
