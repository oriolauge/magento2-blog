<?php
namespace OAG\Blog\Controller\Index;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url as BlogUrl;
use OAG\Blog\Model\Hreflang;

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
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var Hreflang
     */
    protected $hreflang;

    /**
     * Init dependencies
     *
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     * @param PostRepositoryInterface $postRepository
     * @param Config $config
     * @param BlogUrl $blogUrl
     * @param UrlInterface $url
     * @param ForwardFactory $forwardFactory
     * @param Hreflang $hreflang
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        Config $config,
        BlogUrl $blogUrl,
        UrlInterface $url,
        ForwardFactory $forwardFactory,
        Hreflang $hreflang
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
        $this->config = $config;
        $this->blogUrl = $blogUrl;
        $this->url = $url;
        $this->forwardFactory = $forwardFactory;
        $this->hreflang = $hreflang;
    }
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //Is extension is not enabled, we will return 404
        if (!$this->config->isExtensionEnabled()) {
            $resultForward = $this->forwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();
        $this->prepareHeaderValues($resultPage);
        $this->prepareBreadcrumb($resultPage);
        $this->prepareHreflang($resultPage);
        return $resultPage;
    }

    /**
     * Prepare and add hreflang in post view page
     *
     * @param Page $resultPage
     * @return void
     */
    protected function prepareHreflang(Page $resultPage): void
    {
        if (!$this->config->isHreflangEnabled()) {
            return;
        }

        $hreflang = $this->hreflang->getBlogIndexPageHreflang();

        /**
         * We will check if we have two or more languages. If not, we won't show hreflang.
         * x-default needs to be excluded to know the correct languages number
         */
        $languagesHreflangCount = count(array_filter($hreflang, function($key) {
            return $key !== Hreflang::XDEFAULT;
        }, ARRAY_FILTER_USE_KEY));

        if ($languagesHreflangCount > 1) {
            $pageConfig = $resultPage->getConfig();
            foreach ($hreflang as $language => $blogIndexPageUrl) {
                $pageConfig->addRemotePageAsset(
                    $blogIndexPageUrl,
                    'alternate',
                    ['attributes' => ['hreflang' => $language]]
                );
            }
        }
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
