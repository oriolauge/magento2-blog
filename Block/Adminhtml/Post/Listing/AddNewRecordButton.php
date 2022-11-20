<?php
namespace OAG\Blog\Block\Adminhtml\Post\Listing;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AddNewRecordButton
 */
class AddNewRecordButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->authorization = $context->getAuthorization();
    }

    /**
     * AddNewRecordButton button
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->authorization->isAllowed('OAG_Blog::post_create')) {
            $data = [
                'label' => __('Add New Record'),
                'class' => 'primary',
                'on_click' => sprintf("location.href = '%s';", $this->getAddNewRecordUrl()),
                'sort_order' => 10,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    protected function getAddNewRecordUrl(): string
    {
        return $this->urlBuilder->getUrl('*/*/add');
    }
}
