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

class Designerlist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_designer;
    
    /**
     * 
     * @param \Lof\Designer\Model\Designer $designer
     */
    public function __construct(
        \Lof\Designer\Model\Designer $designer
        ) {
        $this->_designer = $designer;
    }
    
    
    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $designers = $this->_designer->getCollection()
        ->addFieldToFilter('status', '1');
        $listDesigner = array();
        foreach ($designers as $designer) {
            $listDesigner[] = array('label' => $designer->getName(),
                'value' => $designer->getId());
        }
        return $listDesigner;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}