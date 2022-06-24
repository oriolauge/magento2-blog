<?php
namespace OAG\Blog\Api\Data;
use Magento\Eav\Api\Data\AttributeInterface;

/**
 * @api
 */
interface EavAttributeInterface extends AttributeInterface
{
    const IS_WYSIWYG_ENABLED = 'is_wysiwyg_enabled';

    const IS_VISIBLE = 'is_visible';

    const SCOPE_STORE_TEXT = 'store';

    const SCOPE_GLOBAL_TEXT = 'global';

    const SCOPE_WEBSITE_TEXT = 'website';

    /**
     * Enable WYSIWYG flag
     *
     * @return bool|null
     */
    public function getIsWysiwygEnabled();

    public function getScope();
}
