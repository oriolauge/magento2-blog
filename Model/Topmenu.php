<?php
namespace OAG\Blog\Model;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree;
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
     * @param [type] $request
     * @param Tree $tree
     * @return Node|null
     */
    public function getBlogNode(Node $menu, $request, Tree $tree): ?Node
    {
        if (!$this->config->canTopmenuShowItem()) {
            return null;
        }

        if (null == $tree) {
            $tree = new Tree;
        }

        $data = [
            'name'      => $this->config->getBlogTopmenuItemText(),
            'id'        => 'oag-blog-topmenu-item',
            'url'       => $this->url->getBlogIndexUrl(),
            'is_active' => ($request->getModuleName() == 'oagblog')
        ];

        return new Node($data, 'id', $tree, $menu);
    }
}