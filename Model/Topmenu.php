<?php
namespace OAG\Blog\Model;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree;
use Magento\Framework\App\RequestInterface;
use OAG\Blog\Model\System\Config;
use OAG\Blog\Model\Url;

/**
 * Topmenu model
 */
class Topmenu
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Url
     */
    protected $url;

    /**
     * Construct function
     *
     * @param Config $config
     */
    public function __construct(
        Config $config,
        Url $url
    ) {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * Get Blog Node
     *
     * @param Node $menu
     * @param RequestInterface $request
     * @param Tree $tree
     * @return Node|null
     */
    public function getBlogNode(Node $menu, RequestInterface $request, Tree $tree = null): ?Node
    {
        if (!$this->config->canTopmenuShowItem() || !$this->config->isExtensionEnabled()) {
            return null;
        }

        if (null == $tree) {
            $tree = new Tree;
        }

        $data = [
            'name'      => $this->config->getBlogTopmenuItemText(),
            'id'        => 'oag-blog-topmenu-main-item',
            'url'       => $this->url->getBlogIndexUrl()
        ];

        return new Node($data, 'id', $tree, $menu);
    }
}