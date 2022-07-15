<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace OAG\Blog\Model\Post;

use OAG\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Post Image Service
 * 
 * Based on Magento\Catalog\Model\Category\Image;
 */
class Image
{
    private const ATTRIBUTE_NAME = 'image';
    /**
     * @var FileInfo
     */
    private $fileInfo;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param FileInfo $fileInfo
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        FileInfo $fileInfo,
        StoreManagerInterface $storeManager
    ) {
        $this->fileInfo = $fileInfo;
        $this->storeManager = $storeManager;
    }
    /**
     * Resolve post image URL
     *
     * @param PostInterface $post
     * @param string $attributeCode
     * @return string
     * @throws LocalizedException
     */
    public function getUrl(PostInterface $post, string $attributeCode = self::ATTRIBUTE_NAME): string
    {
        $url = '';
        $image = $post->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $store = $this->storeManager->getStore();
                $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                if ($this->fileInfo->isBeginsWithMediaDirectoryPath($image)) {
                    $relativePath = $this->fileInfo->getRelativePathToMediaDirectory($image);
                    $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim($relativePath, '/');
                } elseif (substr($image, 0, 1) !== '/') {
                    $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim(FileInfo::ENTITY_MEDIA_PATH, '/') . '/' . $image;
                } else {
                    $url = $image;
                }
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
}
