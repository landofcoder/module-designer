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
namespace Lof\Designer\Block;
use Magento\Customer\Model\Context as CustomerContext;

class DesignerList extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_designerCollection;

    protected $_collection = null;

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
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

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
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
        ) {
        $this->_designerCollection = $designerCollection;
        $this->_designerHelper = $designerHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function _construct(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('designer_block/enable')) return;
        parent::_construct();
        $carousel_layout = $this->getConfig('designer_block/carousel_layout');
        $template = '';
        if($carousel_layout == 'owl_carousel'){
            $template = 'block/designer_list_owl.phtml';
        }else{
            $template = 'block/designer_list_bootstrap.phtml';
        }
        if(!$this->getTemplate() && $template!=''){
            $this->setTemplate($template);
        }
    }

    public function getConfig($key, $default = '')
    {   
        $widget_key = explode('/', $key);
        if( (count($widget_key)==2) && ($resultData = $this->hasData($widget_key[1])) )
        {
            return $this->getData($widget_key[1]);
        }
        $result = $this->_designerHelper->getConfig($key);
        if($result == ""){
            return $default;
        }
        return $result;
    }

    public function getDesignerCollection()
    {
        if(!$this->_collection) {
            $number_item = $this->getConfig('designer_block/number_item');
            $designerGroups = $this->getConfig('designer_block/designer_groups');
            $store = $this->_storeManager->getStore();
            $collection = $this->_designerCollection->getCollection()
            ->setOrder('position','ASC')
            ->addStoreFilter($store)
            ->addFieldToFilter('status',1);
            $designerGroups = explode(',', $designerGroups);
            if(is_array($designerGroups) && count($designerGroups)>0)
            {
                $collection->addFieldToFilter('group_id',array('in' => $designerGroups));
            }
            $collection->setPageSize($number_item)
            ->setCurPage(1)
            ->setOrder('position','ASC');
            $this->_collection = $collection;
        }
        return $this->_collection;
    }


    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
        'VES_BRAND_LIST',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
        'template' => $this->getTemplate(),
        $this->getProductsCount()
        ];
    }

    public function _toHtml()
    {
        return parent::_toHtml();
    }
}