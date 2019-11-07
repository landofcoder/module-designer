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

class SaveProductDesignerModel implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

     /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\App\ResourceConnection  $resource
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request
        )
    {
        $this->_resource = $resource;
        $this->_coreRegistry = $coreRegistry;
        $this->_request = $request;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_designer_product');
        $_product = $observer->getProduct();  // you will get product object
        $productId = $_product->getId();
        $is_saved_designer = $this->_coreRegistry->registry('fired_save_action');
        if(!$is_saved_designer) {
            $data = $this->_request->getPost();;
            if($productId) {
                $connection->query('DELETE FROM ' . $table_name . ' WHERE product_id =  ' . (int)$productId . ' ');
            }
            if($data && isset($data['product']['product_designer']) && $productId){
                $productDesigners = $data['product']['product_designer'];
                if(!is_array($productDesigners)){
                    $productDesigners = array();
                    $productDesigners[] = (int)$data['product']['product_designer'];
                }
                foreach ($productDesigners as $k => $v) {
                    if($v) {
                        $connection->query('INSERT INTO ' . $table_name . ' VALUES ( ' . $v . ', ' . (int)$productId . ',0)');
                    }
                }
            }
            $this->_coreRegistry->register('fired_save_action', true);
        }
    }
}
