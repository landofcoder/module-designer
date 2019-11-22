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
class Designer extends \Magento\Framework\Model\AbstractModel
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

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_designerHelper;

    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                           
     * @param \Lof\Designer\Model\ResourceModel\Designer|null                      $resource                 
     * @param \Lof\Designer\Model\ResourceModel\Designer\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Lof\Designer\Helper\Data                                    $designerHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Designer\Model\ResourceModel\Designer $resource = null,
        \Lof\Designer\Model\ResourceModel\Designer\Collection $resourceCollection = null,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Lof\Designer\Helper\Data $designerHelper,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Lof\Designer\Model\ResourceModel\Designer');
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

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Get category products collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('product_designer',array('eq'=>$this->getId()));
        return $collection;
    }

    /**
     * Get total products number
     *
     * @return int
     */
    public function getTotalProducts()
    {
        if(!$this->hasData("total_products")){
            $total_products = $this->_getResource()->getTotalProducts($this->getId());
            $this->setData("total_products", $total_products);
        }else {
            $total_products = $this->getData("total_products");
        }
        return $total_products;
    }

    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $route = $this->_designerHelper->getConfig('general_settings/route');
        $url_prefix = $this->_designerHelper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        $url_suffix = $this->_designerHelper->getConfig('general_settings/url_suffix');
        return $url.$urlPrefix.$this->getUrlKey().$url_suffix;
    }

    /**
     * Retrive image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $image;
        };
        return $url;
    }

    public function loadByDesignerName($designer_name = "") {
        if($designer_name) {
            $designer_id = $this->_getResource()->getDesignerIdByName($designer_name);
            if($designer_id) {
                $this->load((int)$designer_id);
            }
        }
        return $this;
    }

    public function saveProduct($product_id = "0") {
        if($product_id) {
            $this->_getResource()->saveProduct($this, $product_id);
        }
        return $this;
    }
    public function saveProducts($product_ids = array()) {
        if($product_ids) {
            $this->_getResource()->saveProduct($this, $product_ids);
        }
        return $this;
    }
    

    public function deleteDesignersByProduct($product_id = "0"){
        if($product_id) {
            $this->_getResource()->deleteDesignersByProduct($product_id);
        }
        return $this;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $url = false;
        $thumbnail = $this->getThumbnail();
        if ($thumbnail) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $thumbnail;
        };
        return $url;
    }

}