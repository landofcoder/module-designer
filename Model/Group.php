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
namespace Lof\Designer\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Designer Model
 */
class Group extends \Magento\Framework\Model\AbstractModel
{	
	/**
	 * Designer's Statuses
	 */
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_designerHelper;

    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                             
     * @param \Lof\Designer\Model\ResourceModel\Group|null                      $resource                 
     * @param \Lof\Designer\Model\ResourceModel\Group\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Designer\Model\ResourceModel\Group $resource = null,
        \Lof\Designer\Model\ResourceModel\Group\Collection $resourceCollection = null,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

	/**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Lof\Designer\Model\ResourceModel\Group');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $store = $this->_storeManager->getStore();
        $route = $this->_scopeConfig->getValue(
            'lofdesigner/general_settings/route',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        $url_prefix = $this->_scopeConfig->getValue(
            'lofdesigner/general_settings/url_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        $url_suffix = $this->_scopeConfig->getValue(
            'lofdesigner/general_settings/url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url.$urlPrefix.$this->getUrlKey().$url_suffix;
    }
}