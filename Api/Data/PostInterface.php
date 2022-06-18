<?php
 
namespace OAG\Blog\Api\Data;
use Magento\Framework\Api\ExtensibleDataInterface;

interface PostInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    const KEY_MAIN_TITLE = 'main_title';
    const KEY_STORE_ID = 'store_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getMainTitle();

    /**
    * @param string $name
    * @return void
    */
    public function setMainTitle($name);

    /**
     * @return string
     */
    public function getUrl();
}
