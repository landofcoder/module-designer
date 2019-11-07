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
namespace Lof\Designer\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource;

class Designer extends \Magento\Catalog\Model\Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
    	$designer = $this->getCurrentDesigner();
    	if(isset($this->_productCollections[$designer->getId()])){
    		$collection = $this->_productCollections;
    	}else{
    		$collection = $designer->getProductCollection();
    		$this->prepareProductCollection($collection);
            $this->_productCollections[$designer->getId()] = $collection;
    	} 
    	return $collection;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentDesigner()
    {
    	$designer = $this->getData('current_designer');
    	if ($designer === null) {
    		$designer = $this->registry->registry('current_designer');
    		if ($designer) {
    			$this->setData('current_designer', $designer);
    		}
    	}
    	return $designer;
    }
}