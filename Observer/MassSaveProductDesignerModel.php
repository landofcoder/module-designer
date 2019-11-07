<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Designer
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Designer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\App\Action;

/**
 * Class Save
 */
class MassSaveProductDesignerModel implements ObserverInterface
{

    /**
     * @var  \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    /**
     * @var      \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var
     */
    protected $productCollection;

     /**
      *  @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
      */
     protected $attributeHelper;


     protected $request;
     /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->messageManager = $messageManager;
        $this->attributeHelper = $attributeHelper;
        $this->request = $request;
        $this->_resource = $resource;
    }

     /**
      * @return \Magento\Framework\App\Request\Http
      */
     protected function getRequest()
     {
         return $this->request;
     }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function getProductCollection(){
        if(!$this->productCollection){
            $this->productCollection = $this->attributeHelper->getProducts();
        }
        return $this->productCollection;
    }

    /**
     * @param array $categoryIds
     */
    public function addProductToDesigners($designerIds=[]){
        if(!count($designerIds)){
            return;
        }
        $productDesigners = $designerIds;
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_designer_product');
        
        foreach($this->getProductCollection() as $product) {
        	$productId = $product->getId();
        	$connection->query('DELETE FROM ' . $table_name . ' WHERE product_id =  ' . (int)$productId . ' ');
        	if(!is_array($productDesigners)){
                $productDesigners = array();
                $productDesigners[] = (int)$designerIds;
            }
            foreach ($productDesigners as $k => $v) {
                if($v) {
                    $connection->query('INSERT INTO ' . $table_name . ' VALUES ( ' . $v . ', ' . (int)$productId . ',0)');
                }
            }
        }
    }

     /**
      * @param \Magento\Framework\Event\Observer $observer
      * @return mixed
      */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->getProductCollection()) {
            return ;
        }
        /* Collect Data */
        $attributesData = $this->getRequest()->getParam('attributes', []);
        $attribute_code = "product_designer";

        try {
            if (!empty($attributesData)
                && isset($attributesData[$attribute_code]) && !empty($attributesData[$attribute_code])
            ) {
                $this->addProductToDesigners($attributesData[$attribute_code]);
                $this->messageManager
                    ->addSuccess(__(
                        'A total of %1 record(s) were updated designers.',
                        count($this->attributeHelper->getProductIds())
                    ));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while updating the product(s) designers.')
            );
        }
    }
}
