<?php
/**
 * eav attribute list file
 */
namespace OAG\Blog\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

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

        $attributes['main_title'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'Main Title',
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

        $attributes['url_key'] = [
            'group' => 'General',
            'type' => 'varchar',
            'label' => 'URL key',
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
