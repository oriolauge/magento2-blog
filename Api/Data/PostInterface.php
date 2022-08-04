<?php
 
namespace OAG\Blog\Api\Data;
use Magento\Framework\Api\ExtensibleDataInterface;

interface PostInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    const KEY_TITLE = 'title';
    const KEY_STORE_ID = 'store_id';
    const KEY_ATTR_TYPE_ID = 'attribute_set_id';
    const KEY_CONTENT = 'content';
    const KEY_SHORT_CONTENT = 'short_content';
    const KEY_META_TITLE = 'meta_title';
    const KEY_URL_KEY = 'url_key';
    const KEY_META_DESCRIPTION = 'meta_description';
    const KEY_META_KEYWORDS = 'meta_keywords';
    const KEY_STATUS = 'status';
    const KEY_PUBLISHED_AT = 'published_at';
    const KEY_IMAGE = 'image';
    const KEY_IMAGE_ALT = 'image_alt';
    const KEY_LIST_IMAGE = 'list_image';
    const KEY_LIST_IMAGE_ALT = 'list_image_alt';
    const KEY_OPEN_GRAPH_TITLE = 'open_graph_title';
    const KEY_OPEN_GRAPH_DESCRIPTION = 'open_graph_description';
    const KEY_OPEN_GRAPH_IMAGE = 'open_graph_image';


    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTitle();

    /**
    * @param string $name
    * @return void
    */
    public function setTitle($name);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return int
     */
    public function getAttributeSetId();

    /**
     * @param int $attrSetId
     * @return $this
     */
    public function setAttributeSetId($attrSetId);

    /**
     * @return array
     */
    public function getData();

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId();

    /**
     * Get post content
     *
     * @param bool $processHtml
     * @return string
     */
    public function getContent($processHtml = true);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * Get store Id
     *
     * @return int
     */
    public function getStoreId();
}
