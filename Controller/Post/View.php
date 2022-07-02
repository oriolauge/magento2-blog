<?php
namespace OAG\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Api\PostRepositoryInterface;

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
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
    }
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postId = (int) $this->request->getParam('id');
        $page = $this->resultPageFactory->create();

        $post = $this->postRepository->getById($postId);
       
        $block = $page->getLayout()->getBlock('oagblog_post_view_content');
        $block->setData('custom_parameter', $post->getContent());
        return $page;
    }
}
