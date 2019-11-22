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
 * @copyright  Copyright (c) 209 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Designer\Controller\Adminhtml\Designer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Migration extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Lof\Designer\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository $_productAttributeRepository
     */
    protected $_productAttributeRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Lof\Designer\Helper\Data                  $viewHelper
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute $entityAttribute
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entityAttributeCollection
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $entityAttributeOptionCollection
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Lof\Designer\Helper\Data                  $viewHelper,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entityAttributeCollection, 
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $entityAttributeOptionCollection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection,
        CollectionFactory $collectionFactory
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->_helperData = $viewHelper;
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_entityAttribute = $entityAttribute;
        $this->_entityAttributeCollection = $entityAttributeCollection; 
        $this->_entityAttributeOptionCollection = $entityAttributeOptionCollection;
        $this->_attributeOptionCollection = $attributeOptionCollection;
        $this->collectionFactory =$collectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Lof_Designer::designer_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$attribute_code = $this->_helperData->getConfig('migration_settings/attribute_code');
        $default_group_id = $this->_helperData->getConfig('migration_settings/default_group_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($attribute_code) {
            $attribute_values = $this->getAttrAllOptions($attribute_code);
            if($attribute_values){
                $model = $this->_objectManager->create('Lof\Designer\Model\Designer');
                
                foreach($attribute_values as $key => $val){

                    if(is_numeric($key)){
                        //get product by attribute option value id $key or option value label $val
                    }elseif($key && is_numeric($val)){
                        $designer_id = 0;
                        $modelDesigner = $model->loadByDesignerName($key);
                        if($_id = $modelDesigner->getId()){
                            $designer_id = $_id;
                        }else {
                            $key = trim($key);
                            $url_key = strtolower($key);
                            $url_key = str_replace(" ","-", $url_key);
                            $url_key = preg_replace('/[^a-zA-Z0-9]/', '', $url_key);
                            $data = ['name' => $key,
                                'url_key' => $url_key,
                                'group_id' => (int)$default_group_id,
                                'store_id' => 0,
                                'position' => 0,
                                'status' => 1];

                            $modelDesigner = $this->_objectManager->create('Lof\Designer\Model\Designer');
                            $modelDesigner->setData($data);
                            try {
                                $modelDesigner->save();
                                $designer_id = $modelDesigner->getId();
                                $this->messageManager->addSuccess(__('You saved this designer %1.', $designer_id));
                            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\RuntimeException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\Exception $e) {
                                $this->messageManager->addException($e, __('Something went wrong while saving the designer.'));
                            }
                        }
                        if($designer_id){
                            $modelDesigner->saveProduct((int)$val);
                        }
                    }
                }
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function getAttrAllOptions($attribute_code = "") {
        $AllOptionsArr = [];
        if($attribute_code){
            $designerOptions = $this->_productAttributeRepository->get($attribute_code)->getOptions();
            foreach ($designerOptions as $designerOption) {
                $AllOptionsArr[$designerOption->getValue()] = $designerOption->getLabel();  // Value
            }
            if(empty($AllOptionsArr)){
                $product_collection = $this->getProductCollection();
                if($product_collection){
                    foreach($product_collection as $_product){
                        $option_value = $_product->getResource()->getAttribute($attribute_code)->getFrontend()->getValue($_product);
                        if($option_value){
                            $AllOptionsArr[$option_value] = $_product->getId();
                        }
                    }
                }
            }
        }
        
        return $AllOptionsArr;
    }

    public function getProductCollection()
    { 
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        return $collection;
    }

    public function getAttributeInfo($entityType, $attributeCode)
    {
        return $this->_entityAttribute->loadByCode($entityType, $attributeCode);
    }
    /**
     * Get all options name and value of the attribute
     *
     * @param int $attributeId
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    public function getAttributeOptionAll($attributeId)
    {
        return $this->_attributeOptionCollection
                    ->setPositionOrder('asc')
                    ->setAttributeFilter($attributeId)
                    ->setStoreFilter()
                    ->load();
    }
}