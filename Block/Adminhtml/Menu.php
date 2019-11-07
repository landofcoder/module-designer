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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Designer\Block\Adminhtml;

use Lof\All\Model\Config;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Lof_All::menu.phtml';


    public function __construct(\Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);

    }//end __construct()


    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                      'designer' => [
                            'title' => __('Manage Designers'),
                            'url' => $this->getUrl('*/designer/index'),
                            'resource' => 'Lof_Designer::designer',
                            'child' => [
                                'designer/new/' => [
                                    'title' => __('New Designer'),
                                    'url' => $this->getUrl('*/designer/new/'),
                                    'resource' => 'Lof_Designer::designer_edit',
                                ]
                            ]
                        ],
                        'group' => [
                            'title' => __('Manage Groups'),
                            'url' => $this->getUrl('*/group/index'),
                            'resource' => 'Lof_Designer::group',
                            'child' => [
                                'group/new' => [
                                    'title' => __('New Group'),
                                    'url' => $this->getUrl('*/group/new'),
                                    'resource' => 'Lof_Designer::group_edit',
                                ]
                            ]
                        ],
                        'import' => [
                                     'title'    => __('Import Designers Products'),
                                     'url'      => $this->getUrl('*/import/index'),
                                     'resource' => 'Lof_Designer::import',
                                    ],
                      'settings' => [
                                     'title'    => __('Settings'),
                                     'url'      => $this->getUrl('adminhtml/system_config/edit/section/lofdesigner'),
                                     'resource' => 'Lof_Designer::config_designer',
                                    ],
                      'support'  => [
                                     'title' => __('Get Support'),
                                     'url'   => Config::LANDOFCODER_TICKET,
                                     'attr'  => ['target' => '_blank'],
                                     'separator' => true,
                                    ],
                     ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }

            $this->items = $items;
        }//end if

        return $this->items;

    }//end getMenuItems()


    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items          = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();

        $key = $controllerName . '/' . $actionName;
        if (array_key_exists($key, $items)) {
            return $items[$key];
        }

        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }

        return $items['page'];

    }//end getCurrentItem()


    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }

        return $result;

    }//end renderAttributes()


    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();
        $key = $controllerName . '/' . $actionName;
        if ($key == $itemIndex) {
            return true;
        }
        return $itemIndex == $this->getRequest()->getControllerName();

    }//end isCurrent()


}//end class
