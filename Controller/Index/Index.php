<?php
namespace OAG\Blog\Controller\Index;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Model\System\Config;

/**
 * Blog home page view
 */
class Index implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        Config $config
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
        $this->config = $config;
    }
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $this->prepareHeaderValues($resultPage);
        return $resultPage;
    }

    /**
     * Set all headers values
     *
     * @param Page $resultPage
     * @return void
     */
    protected function prepareHeaderValues(Page $resultPage): void
    {
        $pageConfig = $resultPage->getConfig();
        $metaTitle = $this->config->getBlogMetaTitle();
        $title = $this->config->getBlogTitle();
        if (!empty($metaTitle)) {
            $pageConfig->setMetaTitle($metaTitle);
        } else {
            $pageConfig->setMetaTitle($title);
        }
        $pageConfig->getTitle()->set($title);
    }
}
