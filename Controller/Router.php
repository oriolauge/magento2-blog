<?php
/**
 * Controller to convert SEO url to Magento url
 */

namespace OAG\Blog\Controller;

use Magefan\Blog\Model\Config\Source\BlogPages;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use OAG\Blog\Api\UrlFinderInterface;

/**
 * Class Router
 * @package OAG\Blog\Controller
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @param ActionFactory $actionFactory
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        ActionFactory $actionFactory,
        UrlFinderInterface $urlFinder
    ) {
        $this->actionFactory = $actionFactory;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfoTrimmed = trim($request->getPathInfo(), '/');

        /**
         * We only process this function if we have some value in
         * request, but for security reason, we will check if is
         * emtpy our value
         */
        if (empty($pathInfoTrimmed)) {
            return null;
        }

        $blogPage = $this->urlFinder->resolve($pathInfoTrimmed);

        if ($blogPage) {
            $request->setControllerName($blogPage['controller'])
                ->setActionName($blogPage['action'])
                ->setPathInfo('/oagblog/' . $blogPage['controller'] . '/' . $blogPage['action']);
            
            if (!empty($blogPage['extra_params']) && is_array($blogPage['extra_params'])) {
                $request->setParams($blogPage['extra_params']);
            }
            return $this->actionFactory->create(Forward::class, ['request' => $request]);
        }

        return null;
    }
}
