<?php

namespace OAG\Blog\Controller\Adminhtml\Post;
use Magento\Catalog\Controller\Adminhtml\Category\Image\Upload as CatalogImageUpload;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class Upload
 * 
 * We will reuse catalog category image upload for now to save image in tmp directory
 * and not need to rewrite this code
 */
class Upload extends CatalogImageUpload implements HttpPostActionInterface
{
    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('OAG_Blog::post_edit')
            || $this->_authorization->isAllowed('OAG_Blog::post_create');
    }
}
