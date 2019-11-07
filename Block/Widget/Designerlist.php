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
namespace Lof\Designer\Block\Widget;

class Designerlist extends AbstractWidget
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
     * @var \Magento\Cms\Model\Block
     */
    protected $_blockModel;

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
        \Magento\Cms\Model\Block $blockModel,
        array $data = []
        ) {
        $this->_designerCollection = $designerCollection;
        $this->_designerHelper = $designerHelper;
        $this->_coreRegistry = $registry;
        $this->_blockModel = $blockModel;
        parent::__construct($context, $designerHelper);
    }

    public function getCmsBlockModel(){
        return $this->_blockModel;
    }

    public function _toHtml()
    {
        if(!$this->_designerHelper->getConfig('general_settings/enable')) return;
        $carousel_layout = $this->getConfig('carousel_layout');
        if($carousel_layout == 'owl_carousel'){
            $this->setTemplate('widget/designer_list_owl.phtml');
        }else{
            $this->setTemplate('widget/designer_list_bootstrap.phtml');
        }
        if(($template = $this->getConfig('template')) != ''){
            $this->setTemplate($template);
        }
        return parent::_toHtml();
    }

    public function getDesignerCollection()
    {
        $number_item = $this->getConfig('number_item',12);
        $designerGroups = $this->getConfig('designer_groups');
        $store = $this->_storeManager->getStore();
        $collection = $this->_designerCollection->getCollection()
        ->addFieldToFilter('status',1)
        ->addStoreFilter($store, false);

        $designerGroups = explode(',', $designerGroups);
        if(is_array($designerGroups))
        {
            $collection->addFieldToFilter('group_id',array('in' => $designerGroups));
        }
        $collection->setPageSize($number_item)
        ->setCurPage(1)
        ->setOrder('position','ASC');
        return $collection;
    }
}