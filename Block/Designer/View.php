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
namespace Lof\Designer\Block\Designer;

class View extends \Magento\Framework\View\Element\Template
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_designerHelper;

    protected $_groupModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context       
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver 
     * @param \Magento\Framework\Registry                      $registry      
     * @param \Lof\Designer\Helper\Data                           $designerHelper   
     * @param \Lof\Designer\Model\Group                           $groupModel    
     * @param array                                            $data          
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Catalog\Model\Layer\Resolver $layerResolver,
    	\Magento\Framework\Registry $registry,
    	\Lof\Designer\Helper\Data $designerHelper,
        \Lof\Designer\Model\Group $groupModel,
        array $data = []
        ) {
    	$this->_designerHelper = $designerHelper;
    	$this->_catalogLayer = $layerResolver->get();
    	$this->_coreRegistry = $registry;
        $this->_groupModel = $groupModel;
        parent::__construct($context, $data);
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $designer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $designerRoute = $this->_designerHelper->getConfig('general_settings/route');
        $designerRoute = $designerRoute?$designerRoute:"lofdesigner/index/index";
        $page_title = $this->_designerHelper->getConfig('designer_list_page/page_title');
        $designer = $this->getCurrentDesigner();

        $group = false;
        if($groupId = $designer->getGroupId()){
            $group = $this->_groupModel->load($groupId);
        }
        if($breadcrumbsBlock)
        {
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
            ]
            );
        
        $breadcrumbsBlock->addCrumb(
            'lofdesigner',
            [
                'label' => $page_title,
                'title' => $page_title,
                'link' => $baseUrl.$designerRoute
            ]
            );
        
        if($group && $group->getStatus()){
            $breadcrumbsBlock->addCrumb(
                'group',
                [
                'label' => $group->getName(),
                'title' => $group->getName(),
                'link' => $group->getUrl()
                ]
                );
        }

        $breadcrumbsBlock->addCrumb(
            'designer',
            [
                'label' => $designer->getName(),
                'title' => $designer->getName(),
                'link' => ''
            ]
            );
        }
    }

    public function getCurrentDesigner()
    {
        $designer = $this->_coreRegistry->registry('current_designer');
        if ($designer) {
            $this->setData('current_designer', $designer);
        }
        return $designer;
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
    	return $this->getChildHtml('product_list');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $designer = $this->getCurrentDesigner();
        $page_title = $designer->getName();
        $meta_description = $designer->getMetaDescription();
        $meta_keywords = $designer->getMetaKeywords();
        $this->_addBreadcrumbs();
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
    }
}