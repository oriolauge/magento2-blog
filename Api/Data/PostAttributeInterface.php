<?php
namespace OAG\Blog\Api\Data;
use OAG\Blog\Api\Data\EavAttributeInterface;

/**
 * @api
 */
interface PostAttributeInterface extends EavAttributeInterface
{
    /**
     * @todo: we have this code repeated in our code, so we can use this one to not repeat.
     */
    const ENTITY_TYPE_CODE = 'oag_blog_post';
}
