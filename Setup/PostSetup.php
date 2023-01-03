<?php
/**
 * eav attribute list file
 */
namespace OAG\Blog\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use OAG\Blog\Api\Data\PostInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use OAG\Blog\Model\Post\Attribute\Backend\Image;

class PostSetup extends EavSetup
{

    /**
     * Entity type for Post EAV attributes
     */
    const ENTITY_TYPE_CODE = 'oag_blog_post';

    /**
     * Entity type for Post EAV attributes
     */
    const ENTITY_TABLE_CODE = self::ENTITY_TYPE_CODE . '_entity';

    /**
     * EAV Additional attribute table for blog eav entities
     */
    const EAV_ADDITIONAL_ATTRIBUTE_TABLE_CODE = 'oag_blog_eav_attribute';

    /**
     * Holds general group tag
     */
    const GENERAL_GROUP = 'General';

    /**
     * Holds content group tag
     */
    const CONTENT_GROUP = 'Content';

    /**
     * Holds Search Engine Optimization group tag
     */
    const SEO_GROUP = 'Search Engine Optimization';

    /**
     * Holds Open Graph Metadata group tag
     */
    const OG_METADATA_GROUP = 'Open Graph Metadata';

    /**
     * Retrieve Entity Attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getAttributes()
    {
        $attributes = [];

        //General information
        $attributes[PostInterface::KEY_TITLE] = [
            'group' => self::GENERAL_GROUP,
            'type' => 'varchar',
            'label' => 'Title',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '10',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_STATUS] = [
            'group' => self::GENERAL_GROUP,
            'type' => 'int',
            'label' => 'Status',
            'input' => 'boolean',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'source' => Boolean::class,
            'user_defined' => false,
            'default' => Boolean::VALUE_NO,
            'unique' => false,
            'sort_order' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_PUBLISHED_AT] = [
            'group' => self::GENERAL_GROUP,
            'type' => 'datetime',
            'label' => 'Published At',
            'input' => 'datetime',
            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
            'required' => '1',
            'backend' => Datetime::class,
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_PREVIEW_HASH] = [
            'group' => self::GENERAL_GROUP,
            'type' => 'varchar',
            'label' => 'Preview hash',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '40',
            'note' => '',
            'visible' => '0',
            'wysiwyg_enabled' => '0',
        ];

        //Content
        $attributes[PostInterface::KEY_SHORT_CONTENT] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'text',
            'label' => 'Short Content',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '10',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '1',
        ];

        $attributes[PostInterface::KEY_CONTENT] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'text',
            'label' => 'Content',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '1',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '1'
        ];

        $attributes[PostInterface::KEY_IMAGE] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'varchar',
            'label' => 'Image',
            'input' => 'image',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'backend' => Image::class,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_IMAGE_ALT] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'varchar',
            'label' => 'Image Alt',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '30',
            'note' => 'Leave blank to use Title by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_LIST_IMAGE] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'varchar',
            'label' => 'List Image',
            'input' => 'image',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'backend' => Image::class,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_LIST_IMAGE_ALT] = [
            'group' => self::CONTENT_GROUP,
            'type' => 'varchar',
            'label' => 'List Image Alt',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '50',
            'note' => 'Leave blank to use Title by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        //Search Engine Optimization attributes
        $attributes[PostInterface::KEY_URL_KEY] = [
            'group' => self::SEO_GROUP,
            'type' => 'varchar',
            'label' => 'URL Key',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '10',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_META_TITLE] = [
            'group' => self::SEO_GROUP,
            'type' => 'varchar',
            'label' => 'Meta Title',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => 'Leave blank to use Title by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_META_DESCRIPTION] = [
            'group' => self::SEO_GROUP,
            'type' => 'text',
            'label' => 'Meta Description',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '30',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_META_KEYWORDS] = [
            'group' => self::SEO_GROUP,
            'type' => 'text',
            'label' => 'Meta Keywords',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '40',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_OPEN_GRAPH_TITLE] = [
            'group' => self::OG_METADATA_GROUP,
            'type' => 'text',
            'label' => 'Open Graph Title',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '10',
            'note' => 'Leave blank to use Meta Title by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_OPEN_GRAPH_DESCRIPTION] = [
            'group' => self::OG_METADATA_GROUP,
            'type' => 'text',
            'label' => 'Open Graph Description',
            'input' => 'textarea',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => 'Leave blank to use Meta Description by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_OPEN_GRAPH_IMAGE] = [
            'group' => self::OG_METADATA_GROUP,
            'type' => 'text',
            'label' => 'Open Graph Image',
            'input' => 'image',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'backend' => Image::class,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '30',
            'note' => 'Leave blank to use Image by default.',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];
        // Add your more entity attributes here...

        return $attributes;
    }

    /**
     * Retrieve default entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return [
            self::ENTITY_TYPE_CODE => [
                'entity_model' => 'OAG\Blog\Model\ResourceModel\Post',
                'attribute_model' => 'OAG\Blog\Model\ResourceModel\Eav\Attribute',
                'table' => self::ENTITY_TABLE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => self::EAV_ADDITIONAL_ATTRIBUTE_TABLE_CODE,
                'entity_attribute_collection' => 'OAG\Blog\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];
    }
}
