<?php
namespace OAG\Blog\Block\Adminhtml\Post\Edit;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Preview post button in UI Form
 */
class Preview implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * RequestInterface
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Init dependencies
     *
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * Get preview button data to generate button in admin form
     *
     * @return array
     */
    public function getButtonData()
    {
        if (($id = (int) $this->request->getParam('entity_id')) > 0) {
            $storeId = (int) $this->request->getParam('store');
            if ($storeId < 1) {
                $storeId = $this->storeManager->getDefaultStoreView()->getStoreId();
            }
            $data = [
                'label' => __('Preview'),
                'class' => 'preview',
                'id' => 'post-edit-preview-button',
                'data_attribute' => [
                    'url' => $this->getPreviewUrl($id, $storeId),
                ],
                'on_click' =>
                    "window.open('" . $this->getPreviewUrl($id, $storeId) . "','_blank')",
                'sort_order' => 30,
            ];
            return $data;
        }
    }

    /**
     * Preview records controller URL
     *
     * @param integer $id
     * @param integer $storeId
     * @return string
     */
    public function getPreviewUrl(int $id, int $storeId): string
    {
        return $this->urlBuilder->getUrl('*/*/preview', [
            'entity_id' => $id,
            'store_id' => $storeId
        ]);
    }
}
