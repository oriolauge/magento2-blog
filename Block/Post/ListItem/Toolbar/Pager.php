<?php
namespace OAG\Blog\Block\Post\ListItem\Toolbar;
use Magento\Framework\View\Element\Template\Context;
use OAG\Blog\Model\Url;


/**
 * Blog posts list toolbar pager
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var Url;
     */
    protected $url;

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        array $data = []
    ) {
        $this->url = $url;
        parent::__construct($context, $data);
    }

    /**
     * Return current URL with rewrites and additional parameters
     * 
     * We added _direct param to avoid urls like oagblog/index/index
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;
        $urlParams['_direct'] = $this->url->getBlogIndexRelativeUrl();

        return $this->getUrl($this->getPath(), $urlParams);
    }
}
