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
namespace Lof\Designer\Model\ResourceModel\Group;

use \Lof\Designer\Model\ResourceModel\AbstractCollection;
/**
 * Designer collection
 */
class Collection extends AbstractCollection
{

	/**
     * @var string
     */
	protected $_idFieldName = 'group_id';

	/**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
	{
		$this->_init('Lof\Designer\Model\Group', 'Lof\Designer\Model\ResourceModel\Group');
		$this->_map['fields']['group_id'] = 'main_table.group_id';
	}

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}