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

class PostSetup extends EavSetup
{

    /**
     * Entity type for Post EAV attributes
     */
    const ENTITY_TYPE_CODE = 'oag_blog_post';

    /**
     * EAV Entity type for Blog EAV attributes
     */
    const EAV_ENTITY_TYPE_CODE = 'oag_blog';

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
            'group' => 'General',
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
            'group' => 'General',
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
            'group' => 'General',
            'type' => 'datetime',
            'label' => 'Published at',
            'input' => 'date',
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


        //Content

        $attributes[PostInterface::KEY_SHORT_CONTENT] = [
            'group' => 'Content',
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
            'group' => 'Content',
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
            'wysiwyg_enabled' => '1',
        ];

        $attributes[PostInterface::KEY_IMAGE] = [
            'group' => 'Content',
            'type' => 'varchar',
            'label' => 'Image',
            'input' => 'media_image',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        //Search Engine Optimization attributes
        $attributes[PostInterface::KEY_URL_KEY] = [
            'group' => 'Search Engine Optimization',
            'type' => 'varchar',
            'label' => 'URL key',
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
            'group' => 'Search Engine Optimization',
            'type' => 'varchar',
            'label' => 'Meta Title',
            'input' => 'text',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'required' => '0',
            'user_defined' => false,
            'default' => '',
            'unique' => false,
            'sort_order' => '20',
            'note' => '',
            'visible' => '1',
            'wysiwyg_enabled' => '0',
        ];

        $attributes[PostInterface::KEY_META_DESCRIPTION] = [
            'group' => 'Search Engine Optimization',
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
            'group' => 'Search Engine Optimization',
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
                'table' => self::ENTITY_TYPE_CODE,
                'increment_model' => null,
                'additional_attribute_table' => 'oag_blog_eav_attribute',
                'entity_attribute_collection' => 'OAG\Blog\Model\ResourceModel\Attribute\Collection',
                'attributes' => $this->getAttributes(),
            ],
        ];
    }
}
