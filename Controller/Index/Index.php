<?php
namespace OAG\Blog\Controller\Index;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\UrlInterface;
use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url as BlogUrl;

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
     * @var BlogUrl
     */
    protected $blogUrl;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * constructor function
     *
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     * @param PostRepositoryInterface $postRepository
     * @param Config $config
     * @param BlogUrl $blogUrl
     * @param UrlInterface $url
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        Config $config,
        BlogUrl $blogUrl,
        UrlInterface $url
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
        $this->config = $config;
        $this->blogUrl = $blogUrl;
        $this->url = $url;
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
        $this->prepareBreadcrumb($resultPage);
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
        if ($metaKeywords = $this->config->getBlogMetaKeywords()) {
            $pageConfig->setKeywords($metaKeywords);
        }
        if ($metaDescription = $this->config->getBlogMetaDescription()) {
            $pageConfig->setDescription($metaDescription);
        }
        $pageConfig->addRemotePageAsset(
            $this->blogUrl->getBlogIndexUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
    }

    /**
     * Prepare breadcrumb for post pages
     *
     * @param Page $resultPage
     * @return void
     */
    protected function prepareBreadcrumb(Page $resultPage): void
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->url->getUrl()
            ]
        );
        $breadcrumbs->addCrumb('oag_blog', [
                'label' => $this->config->getBlogTitle(),
                'title' => $this->config->getBlogTitle(),
            ]
        );
    }
}
