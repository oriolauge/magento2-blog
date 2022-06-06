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
        $blogRoute = $this->config->getBlogRoute();

        //No blog url
        if (empty($blogRoute) || $urlExplode[0] != $blogRoute) {
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
            $postId = $this->getPostId(array_pop($urlExplode), $storeId);
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
     * Return post id
     *
     * @param string $urlKey
     * @return int|null
     */
    protected function getPostId(string $urlKey, $storeId): ?int
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