<?php
/**
 * Class to try to find post with entered url
 */
namespace OAG\Blog\Model;
use Magento\Store\Model\StoreManagerInterface;
use OAG\Blog\Api\UrlFinderInterface;
use OAG\Blog\Model\Config;
use OAG\Blog\Model\ResourceModel\Post\Collection as PostCollection;

class UrlFinder implements UrlFinderInterface
{
    /**
     * @var PostCollection
     */
    protected $postCollection;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * UrlFinder constructor
     *
     * @param Config $config
     */
    public function __construct(
        Config $config,
        PostCollection $postCollection,
        StoreManagerInterface $storeManager
    )
    {
        $this->config = $config;
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
        } else if (count($urlExplode) == 1) {
            //Check blog url
            $storeId = $this->storeManager->getStore()->getId();
            $postId = $this->getPostIdByUrlKey(array_pop($urlExplode), $storeId);
            if ($postId) {
                return [
                    'controller' => 'post',
                    'action' => 'view',
                    'extra_params' => [
                        'id' => $postId
                    ]
                ];
            }
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
        $blogRoute = $this->config->getBlogRoute();
        if (empty($blogRoute)) {
            return false;
        }

        if (empty($blogRoute) || $url != $blogRoute) {
            return false;
        }

        return true;
    }

    /**
     * Return post id
     *
     * @param string $urlKey
     * @return int|null
     */
    public function getPostIdByUrlKey(string $urlKey, $storeId): ?int
    {
        //remove sufix url
        $urlBlog = str_replace(
            $this->config->getPostSufix()
            , ''
            , $urlKey
        );
        $this->postCollection
            ->setStoreId($storeId)
            ->addFieldToFilter('url_key', ['eq' => $urlBlog]);
        $ids = $this->postCollection->getAllIds();
        //return the first one
        if (is_array($ids) && count($ids) > 0) {
            return $ids[0];
        }

        return null;
    }
}