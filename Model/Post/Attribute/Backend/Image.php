<?php
namespace OAG\Blog\Model\Post\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Blog post image attribute backend model
 */
class Image extends AbstractBackend
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader,
        Filesystem $filesystem
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageUploader = $imageUploader;
    }

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
        $attributeName = $this->getAttribute()->getName();
        $value = $object->getData($attributeName);

        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            /** @var StoreInterface $store */
            $store = $this->storeManager->getStore();
            $baseMediaDir = $store->getBaseMediaDir();
            $newImgRelativePath = $this->imageUploader->moveFileFromTmp($imageName, true);
            $value[0]['url'] = '/' . $baseMediaDir . '/' . $newImgRelativePath;
            $value[0]['name'] = $value[0]['url'];
        } elseif ($this->fileResidesOutsidePostDir($value)) {
            // use relative path for image attribute so we know it's outside of post dir when we fetch it
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $value[0]['url'] = parse_url($value[0]['url'], PHP_URL_PATH);
            $value[0]['name'] = $value[0]['url'];
        }

        if ($imageName = $this->getUploadedImageName($value)) {
            if (!$this->fileResidesOutsidePostDir($value)) {
                $imageName = $this->checkUniqueImageName($imageName);
            }
            $object->setData($attributeName, $imageName);
        } elseif (!is_string($value)) {
            $object->setData($attributeName, null);
        }

        return parent::beforeSave($object);
    }

    /**
     * Check for file path resides outside of post media dir.
     * The URL will be a path including pub/media if true
     *
     * @param array|null $value
     * @return bool
     */
    private function fileResidesOutsidePostDir($value)
    {
        if (!is_array($value) || !isset($value[0]['url'])) {
            return false;
        }

        $fileUrl = ltrim($value[0]['url'], '/');
        $baseMediaDir = $this->filesystem->getUri(DirectoryList::MEDIA);

        if (!$baseMediaDir) {
            return false;
        }

        return strpos($fileUrl, $baseMediaDir) !== false;
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    private function isTmpFileAvailable($value): bool
    {
        return is_array($value) && !empty($value[0]['tmp_name']);
    }

    /**
     * Gets image name from $value array.
     *
     * Will return empty string in a case when $value is not an array.
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value): string
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    /**
     * Check that image name exists in blog/post directory and return
     * new image name if it already exists.
     *
     * @param string $imageName
     * @return string
     */
    private function checkUniqueImageName(string $imageName): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $imageAbsolutePath = $mediaDirectory->getAbsolutePath(
            $this->imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $imageName
        );

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $imageName = call_user_func([Uploader::class, 'getNewFilename'], $imageAbsolutePath);

        return $imageName;
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
