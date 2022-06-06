<?php
namespace OAG\Blog\Controller\Index;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Blog home page view
 */
class Index implements ActionInterface, HttpGetActionInterface
{
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        die("hola!");
    }
}
