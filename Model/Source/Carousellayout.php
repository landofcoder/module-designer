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
namespace Lof\Designer\Model\Source;

class Carousellayout implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_group;
    
    /**
     * 
     * @param \Lof\Designer\Model\Group $group
     */
    public function __construct(
        \Lof\Designer\Model\Group $group
        ) {
        $this->_group = $group;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {   
        $groupList = array();
        $groupList[] = array(
            'label' => __('Owl Carousel'),
            'value' => 'owl_carousel'
            );
        
        $groupList[] = array(
            'label' => __('Bootstrap Carousel'),
            'value' => 'bootstrap_carousel'
            );
        return $groupList;
    }
}
