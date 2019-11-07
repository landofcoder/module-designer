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
namespace Lof\Designer\Block\Adminhtml\Designer\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Designer Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
                'general',
                [
                    'label' => __('Designer Information'),
                    'content' => $this->getLayout()->createBlock('Lof\Designer\Block\Adminhtml\Designer\Edit\Tab\Main')->toHtml()
                ]
            );

        $this->addTab(
                'products',
                [
                    'label' => __('Products'),
                    'url' => $this->getUrl('lofdesigner/*/products', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );

        $this->addTab(
                'design',
                [
                    'label' => __('Design'),
                    'content' => $this->getLayout()->createBlock('Lof\Designer\Block\Adminhtml\Designer\Edit\Tab\Design')->toHtml()
                ]
            );

        $this->addTab(
                'meta',
                [
                    'label' => __('Meta Data'),
                    'content' => $this->getLayout()->createBlock('Lof\Designer\Block\Adminhtml\Designer\Edit\Tab\Meta')->toHtml()
                ]
            );
    }
}
