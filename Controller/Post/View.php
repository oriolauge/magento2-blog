<?php
namespace OAG\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use OAG\Blog\Api\Data\PostInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\UrlInterface;

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
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        ForwardFactory $forwardFactory,
        UrlInterface $url
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
        $this->forwardFactory = $forwardFactory;
        $this->url = $url;
    }

    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postId = (int) $this->request->getParam('id');
        try {
            $post = $this->postRepository->getById($postId);
        } catch (NoSuchEntityException $e) {
            //if post not exists... return 404
            $resultForward = $this->forwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $resultPage = $this->resultPageFactory->create();
        $this->prepareHeaderValues($resultPage, $post);
        $this->prepareBreadcrumb($resultPage, $post);
        $block = $resultPage->getLayout()->getBlock('oagblog_post_view_content');
        $block->setData('custom_parameter', $post->getContent());
        return $resultPage;

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
        $metaTitle = $post->getMetaTitle();
        $pageConfig->setMetaTitle($metaTitle);
        $pageConfig->getTitle()->set($metaTitle);
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
    protected function prepareBreadcrumb(Page $resultPage, PostInterface $post)
    {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->url->getUrl()
            ]
        );
        $breadcrumbs->addCrumb('oag_blog_post', [
                'label' => $post->getTitle(),
                'title' => $post->getTitle(),
            ]
        );
    }
}
