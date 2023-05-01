<?php
namespace OAG\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\UrlInterface;
use OAG\Blog\Api\PostRepositoryInterface;
use OAG\Blog\Api\Data\PostInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url as BlogUrl;
use OAG\Blog\Block\Post\View as PostView;
use OAG\Blog\Block\Post\View\Opengraph;
use OAG\Blog\Block\Post\View\Richsnippets;
use OAG\Blog\Model\Hreflang;

/**
 * Blog home page view
 */
class View implements HttpGetActionInterface
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
     * @var ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var BlogUrl
     */
    protected $blogUrl;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Hreflang
     */
    protected $hreflang;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        ForwardFactory $forwardFactory,
        UrlInterface $url,
        BlogUrl $blogUrl,
        Config $config,
        Hreflang $hreflang
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
        $this->forwardFactory = $forwardFactory;
        $this->url = $url;
        $this->blogUrl = $blogUrl;
        $this->config = $config;
        $this->hreflang = $hreflang;
    }

    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->config->isExtensionEnabled()) {
            return $this->return404();
        }

        $postId = (int) $this->request->getParam('id');
        $preview = $this->request->getParam(PostInterface::KEY_PREVIEW_HASH);

        try {
            $post = $this->postRepository->getById($postId);
        } catch (NoSuchEntityException $e) {
            //if post not exists... return 404
            return $this->return404();
        }

        //If post is disabled and we don't get the preview hash
        if (!$post->getStatus() && $preview !== $post->getPreviewHash()) {
            return $this->return404();
        }

        $resultPage = $this->resultPageFactory->create();
        $this->prepareHeaderValues($resultPage, $post);
        $this->prepareBreadcrumb($resultPage, $post);
        $this->prepareHrefLang($resultPage, $post);
        $blockPostViewContent = $resultPage->getLayout()->getBlock('oagblog_post_view_content');
        $blockPostViewOpengraph = $resultPage->getLayout()->getBlock('oagblog_post_view_opengraph');
        $blockPostViewRichSnippets = $resultPage->getLayout()->getBlock('oagblog_post_view_richsnippets');
        $blockPostViewNextPrev = $resultPage->getLayout()->getBlock('oagblog_post_view_nextprev');
        $blockPostViewContent->setData(PostView::POST_FIELD, $post);
        $blockPostViewOpengraph->setData(Opengraph::POST_FIELD, $post);
        $blockPostViewRichSnippets->setData(Richsnippets::POST_FIELD, $post);
        $blockPostViewNextPrev->setData(Richsnippets::POST_FIELD, $post);
        return $resultPage;
    }

    /**
     * Prepare and add hreflang in post view page
     *
     * @param Page $resultPage
     * @param PostInterface $post
     * @return void
     */
    protected function prepareHrefLang(Page $resultPage, PostInterface $post): void
    {
        if (!$this->config->isHreflangEnabled()) {
            return;
        }

        $hreflang = $this->hreflang->getPostHreflang($post);

        /**
         * We will check if we have two or more languages. If not, we won't show hreflang.
         * x-default needs to be excluded to know the correct languages number
         */
        $languagesHreflangCount = count(array_filter($hreflang, function($key) {
            return $key !== Hreflang::XDEFAULT;
        }, ARRAY_FILTER_USE_KEY));

        if ($languagesHreflangCount > 1) {
            $pageConfig = $resultPage->getConfig();
            foreach ($hreflang as $language => $postUrl) {
                $pageConfig->addRemotePageAsset(
                    $postUrl,
                    'alternate',
                    ['attributes' => ['hreflang' => $language]]
                );
            }
        }
    }

    /**
     * Return 404 page
     *
     * @return Forward
     */
    protected function return404(): Forward
    {
        $resultForward = $this->forwardFactory->create();
        $resultForward->forward('noroute');
        return $resultForward;
    }

    /**
     * Prepare all headers values to show post
     *
     * @param Page $resultPage
     * @param PostInterface $post
     * @return void
     */
    protected function prepareHeaderValues(Page $resultPage, PostInterface $post)
    {
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setMetaTitle($post->getMetaTitle());
        $pageConfig->getTitle()->set($post->getTitle());
        $pageConfig->setKeywords($post->getMetaKeywords());
        $pageConfig->setDescription($post->getMetaDescription());

        $pageConfig->addRemotePageAsset(
            $post->getUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
    }

    /**
     * Prepare breadcrumb for post pages
     *
     * @param Page $resultPage
     * @param PostInterface $post
     * @return void
     */
    protected function prepareBreadcrumb(Page $resultPage, PostInterface $post): void
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
                'link' => $this->blogUrl->getBlogIndexUrl()
            ]
        );
        $breadcrumbs->addCrumb('oag_blog_post', [
                'label' => $post->getTitle(),
                'title' => $post->getTitle(),
            ]
        );
    }
}
