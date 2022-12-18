<?php
/**
 * mass action column in UI grid
 */
namespace OAG\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\AuthorizationInterface;
use OAG\Blog\Api\Data\PostInterface;

class PostActions extends Column
{

    /**
     * Url path
     */
    const URL_PATH_EDIT = 'oag_blog/post/edit';
    const URL_PATH_DELETE = 'oag_blog/post/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id']) && $this->authorization->isAllowed('OAG_Blog::post_edit')) {
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        ),
                        'label' => __('Edit'),
                        'hidden' => false,
                    ];
                }
                if (isset($item['entity_id']) && $this->authorization->isAllowed('OAG_Blog::post_delete')) {
                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::URL_PATH_DELETE,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete "%1"', $item[PostInterface::KEY_TITLE]),
                            'message' => __('Are you sure you want to delete the "%1" record?'
                                , $item[PostInterface::KEY_TITLE]
                            ),
                        ],
                        'hidden' => false,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
