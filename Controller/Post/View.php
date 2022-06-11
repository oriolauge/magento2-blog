<?php
namespace OAG\Blog\Controller\Post;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Model\PostFactory;

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
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        PageFactory $resultPageFactory,
        PostFactory $postFactory
    )
    {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->postFactory = $postFactory;
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

        $postData = $this->postFactory->create();
        $postData->load($postId);
       
        $block = $page->getLayout()->getBlock('oagblog_post_view_content');
        $block->setData('custom_parameter', $postData->getMainTitle());
        return $page;
    }
}
