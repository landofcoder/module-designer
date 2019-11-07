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
namespace Lof\Designer\Block\Adminhtml\System\Config\Form\Field;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

	/**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
        ) {
    	$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	$config = $this->_wysiwygConfig->getConfig();
        $element->setWysiwyg(true);
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}