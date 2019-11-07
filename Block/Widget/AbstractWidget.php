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
namespace Lof\Designer\Block\Widget;

class AbstractWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	/**
	 * @var \Lof\Designer\Helper\Data
	 */
	protected $_designerHelper;

	/**
     * @param \Magento\Framework\View\Element\Template\Context $context     
     * @param \Lof\Designer\Helper\Data                           $designerHelper 
     * @param array                                            $data        
     */
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\Designer\Helper\Data $designerHelper,
        array $data = []
        ) {
        $this->_designerHelper = $designerHelper;
        parent::__construct($context, $data);
    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }
}