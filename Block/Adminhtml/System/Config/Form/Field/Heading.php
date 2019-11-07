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
use Magento\Config\Block\System\Config\Form\Field;

class Heading extends Field
{

    /**
     * render separator config row
     * 
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
        . '<td class="label" colspan="3">';

        $html .= '<div style="border-bottom: 1px solid #dfdfdf;
        font-size: 15px;
        color: #666;
        border-left: #CCC solid 5px;
        padding: 2px 12px;
        text-align: left !important;
        margin-left: 10%;
        margin-top: 20px;">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        
        return $html;
    }

}
