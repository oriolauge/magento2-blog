<?php
namespace OAG\Blog\Model\Post\Attribute\Backend;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

/**
 * Blog post image attribute backend model
 */
class Image extends AbstractBackend
{
    /**
     * Avoiding saving potential upload data to DB.
     *
     * Will set empty image attribute value if image was not uploaded.
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        return parent::beforeSave($object);
    }

    /**
     * Save uploaded file and set its name to post
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave($object)
    {
        return parent::afterSave($object);
    }
}
