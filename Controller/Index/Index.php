<?php
namespace OAG\Blog\Controller\Index;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Api\PostRepositoryInterface;

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
        return $this->resultPageFactory->create();
    }
}
