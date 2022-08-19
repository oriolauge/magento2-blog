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

    const IS_PAGEBUILDER_ENABLED = 'is_pagebuilder_enabled';

    /**
     * Enable WYSIWYG flag
     *
     * @return bool|null
     */
    public function getIsWysiwygEnabled();

    /**
     * Check if is pagebuilder enabled
     *
     * @return bool|null
     */
    public function getIsPageBuilderEnabled();

    /**
     * Return attribute scope
     *
     * @return string
     */
    public function getScope();
}
