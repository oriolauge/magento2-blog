<?php
/**
 * Class to try to find post with entered url
 */
namespace OAG\Blog\Model;
use Magento\Store\Model\StoreManagerInterface;
use OAG\Blog\Api\UrlFinderInterface;
use OAG\BlogUrlRewrite\Model\MainBlogUrlPathGenerator;
use OAG\Blog\Model\ResourceModel\Post\Collection as PostCollection;

class UrlFinder implements UrlFinderInterface
{
    /**
     * @var PostCollection
     */
    protected $postCollection;

    /**
     * @var MainBlogUrlPathGenerator
     */
    protected $mainBlogUrlPathGenerator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * UrlFinder constructor
     *
     * @param MainBlogUrlPathGenerator $mainBlogUrlPathGenerator
     */
    public function __construct(
        MainBlogUrlPathGenerator $mainBlogUrlPathGenerator,
        PostCollection $postCollection,
        StoreManagerInterface $storeManager
    )
    {
        $this->mainBlogUrlPathGenerator = $mainBlogUrlPathGenerator;
        $this->postCollection = $postCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * Function to resolve url and know if is a post, main page, etc
     *
     * @param string $url
     * @return array|null
     */
    public function resolve(string $url): ?array
    {
        $urlExplode = explode('/', $url);

        //No blog url
        if (!$this->isBlogUrl($urlExplode[0])) {
            return null;
        }

        //Remove blog part and start to check all blog urls
        unset($urlExplode[0]);
        //Default view (main blog page)

        if (!count($urlExplode)) {
            return [
                'controller' => 'index',
                'action' => 'index'
            ];
        }
        return null;
    }

    /**
     * Checks if url is from blog
     *
     * @param string $url
     * @return boolean
     */
    public function isBlogUrl(string $url): bool
    {
        $blogRoute = $this->mainBlogUrlPathGenerator->getMainBlogUrlPathWithSuffix();

        if (empty($blogRoute) || $url != $blogRoute) {
            return false;
        }

        return true;
    }
}