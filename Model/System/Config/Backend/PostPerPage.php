<?php

namespace OAG\Blog\Model\System\Config\Backend;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Post per page backend
 */
class PostPerPage extends Value
{
    /**
     * Check if post per page is a number and not negative
     *
     * @return $this
     */
    public function beforeSave()
    {
        if (!preg_match('/^\d+$/', $this->getValue())) {
            throw new LocalizedException(
                __('Post per page value needs to be a positive and not decimal number.')
            );
        }
        return $this;
    }
}
