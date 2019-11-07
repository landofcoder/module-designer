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
namespace Lof\Designer\Block\Product;
use Magento\Customer\Model\Context as CustomerContext;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_designerCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_designerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry        
     * @param \Lof\Designer\Helper\Data                           $designerHelper     
     * @param \Lof\Designer\Model\Designer                           $designerCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Designer\Helper\Data $designerHelper,
        \Lof\Designer\Model\Designer $designerCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->_designerCollection = $designerCollection;
        $this->_designerHelper = $designerHelper;
        $this->_coreRegistry = $registry;
        $this->_resource = $resource;
        parent::__construct($context, $data); 
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getDesignerCollection(){
        $product = $this->getProduct();
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_designer_product');
        $designerIds = $connection->fetchCol(" SELECT designer_id FROM ".$table_name." WHERE product_id = ".$product->getId());
        if($designerIds || count($designerIds) > 0) {
            $collection = $this->_designerCollection->getCollection()
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('designer_id IN (?)', $designerIds);
            return $collection;
        }
        return false;
    }

    public function _toHtml(){
        if(!$this->_designerHelper->getConfig('product_view_page/enable_designer_info')) return;

        return parent::_toHtml();
    }

}