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

class Resolver extends \Magento\Catalog\Model\Layer\Resolver
{
	/**
     * Get current Catalog Layer
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function get()
    {
        if (!isset($this->layer)) {
            $this->layer = $this->objectManager->create($this->layersPool['category']);
        }
        return $this->layer;
    }
}