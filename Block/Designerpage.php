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

class Designerpage extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\Designer\Helper\Data
     */
    protected $_designerHelper;

    /**
     * @var \Lof\Designer\Model\Designer
     */
    protected $_designer;

    protected $_collection = null;
    

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Lof\Designer\Helper\Data                           $designerHelper  
     * @param \Lof\Designer\Model\Designer                           $designer        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Designer\Helper\Data $designerHelper,
        \Lof\Designer\Model\Designer $designer,
        
        array $data = []
        ) {
        $this->_designer = $designer;
        $this->_coreRegistry = $registry;
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();
        $template = '';
        $layout = $this->getConfig('designer_list_page/layout');
        if($layout == 'grid'){
            $template = 'designerlistpage_grid.phtml';
        }elseif($layout == 'alphabet'){
            $template = 'designerlistpage_alphabet.phtml';
        }elseif($layout == 'newlist'){
            $template = 'designerlistpage_newlist.phtml';
        }else{
            $template = 'designerlistpage_list.phtml';
        }
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
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
        $page_title = $this->_designerHelper->getConfig('designer_list_page/page_title');

        if($breadcrumbsBlock){

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
            'link' => ''
            ]
            );
        }
    }

    /**
     * Set designer collection
     * @param \Lof\Designer\Model\Designer
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive designer collection
     * @param \Lof\Designer\Model\Designer
     */
    public function getCollection()
    {
        if($this->_collection == null){
            $store = $this->_storeManager->getStore();
            
            $designer = $this->_designer;
            $designerCollection = $designer->getCollection()
            ->addFieldToFilter('status',1)
            ->addStoreFilter($store)
            ->setOrder('position','ASC');

            $designerCollection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $designerCollection->setOrder('position','ASC');
            $this->setCollection($designerCollection);

        }
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_designerHelper->getConfig($key);
        if(!$result){

            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('designer_list_page/page_title');
        $meta_description = $this->getConfig('designer_list_page/meta_description');
        $meta_keywords = $this->getConfig('designer_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('lof-designerlist');
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

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofdesigner_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();
        $pretext = $this->getConfig('designer_list_page/pretext');
        if($pretext){
            $pretext                   = $this->_designerHelper->filter($pretext);
        }
        $this->setData("pretext", $pretext);
        
        // set collection to toolbar and apply sort
        $itemsperpage = (int)$this->getConfig('designer_list_page/item_per_page',0);
        if(!$itemsperpage) {
            $toolbar = false;
        }
        if($toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        $layout = $this->getConfig('designer_list_page/layout');
        if($layout == 'alphabet'){
            $collection = $this->sortDesignerByAlphabet($collection);
            $this->setCollection($collection);
        }
        return parent::_beforeToHtml();
    }

    public function getAlphabetLetters(){
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $alphabet;
    }

    public function sortDesignerByAlphabet($collection = null){
        if(!$collection){
            $collection = $this->getCollection();
        }
        $letters = $this->getAlphabetLetters();
        $output = array();
        foreach($letters as $letter){
            $output[$letter] = array();
        }
        $output["#"] = array();

        foreach($collection as $_designer) {
            $designer_name = $_designer->getName();
            $letter = strtoupper(substr($designer_name, 0, 1));
            if(!in_array($letter,$letters)){
                $letter = "#";
            }
            $total_products = $_designer->getTotalProducts();
            $output[ $letter ][] = $_designer; // Or, whatever you want to output.
            
        }
        return $output;
    }
}