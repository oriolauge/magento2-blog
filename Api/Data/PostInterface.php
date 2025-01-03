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
    const KEY_PREVIEW_HASH = 'preview_hash';
    const KEY_UPDATED_AT = 'updated_at';

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
     * Get post url from specific store id
     *
     * @param int $storeId
     * @return void
     */
    public function getUrlByStoreId($storeId);

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
     * Return Url Key by specific Store Id
     * @return string
     */
    public function getUrlKeyByStoreId($storeId);

    /**
     * Return relative url without store code
     *
     * @return string
     */
    public function getRelativeUrl();

    /**
     * Get store Id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Return Meta title value
     * 
     * If Meta title is empty, we will return title by default
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Return Meta keywords value
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Return Meta description value
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Returns Post image url
     *
     * @return string
     */
    public function getImageUrl();

    /**
     * Returns Post list image url
     *
     * @return string
     */
    public function getListImageUrl();

    /**
     * Get Post list image alt
     *
     * @return string|null
     */
    public function getListImageAlt();

    /**
     * Get Post image alt
     *
     * @return string|null
     */
    public function getImageAlt();

    /**
     * Get short content
     *
     * @return string|null
     */
    public function getShortContent();

    /**
     * Get published at
     *
     * @param string $dateFormat
     * @return string
     */
    public function getPublishedAt($dateFormat = null);

    /**
     * Get preview hash. Used to preview a post even if is not enabled
     *
     * @return string
     */
    public function getPreviewHash();

    /**
     * Get post status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Get previous post
     *
     * @return PostInterface
     */
    public function getPreviousPost();

    /**
     * Get next post
     *
     * @return PostInterface
     */
    public function getNextPost();

    /**
     * Get Open Graph Title
     *
     * If Open graph is empty, we will return post meta title
     *
     * @return string
     */
    public function getOpenGraphTitle();

    /**
     * Get Open Graph Description
     *
     * If Open graph is empty, we will return post meta description
     *
     * @return string|null
     */
    public function getOpenGraphDescription();

    /**
     * Get Open Graph Image Url
     *
     * If Open graph is empty, we will return post image
     * @return string
     */
    public function getOpenGraphImageAbsoluteUrl();

    /**
     * Get all stores where the post is enabled
     *
     * @return array
     */
    public function getAllStatusStoreValues();
}
