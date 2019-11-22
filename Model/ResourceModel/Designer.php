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
namespace Lof\Designer\Model\ResourceModel;

class Designer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\Datetime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
        $this->_init('lof_designer','designer_id');
    }

    /**
     *  Check whether designer url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericDesignerUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['lof_designer_store' => $this->getTable('lof_designer_store')],
                $this->getMainTable() . '.designer_id = lof_designer_store.designer_id',
                []
                )->where(
                'status = ?',
                1
                )->where(
                'lof_designer_store.store_id IN (?)',
                $storeIds
                )->order(
                'lof_designer_store.store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
            )->join(
            ['cps' => $this->getTable('lof_designer_store')],
            'cp.designer_id = cps.designer_id',
            []
            )->where(
            'cp.identifier = ?',
            $identifier
            )->where(
            'cps.store_id IN (?)',
            $store
            );

            if (!is_null($isActive)) {
                $select->where('cp.status = ?', $isActive);
            }

            return $select;
        }

    /**
     * Check if designer url key exist for specific store
     * return designer id if designer exists
     *
     * @param string $url_key
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($url_key, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($url_key, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.designer_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Process designer data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['designer_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_designer_store'), $condition);

        $condition = ['designer_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lof_designer_product'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process designer data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $result = $this->checkUrlExits($object); 

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign designer to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('lof_designer_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['designer_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['designer_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }


        // Posts Related
        if(null !== ($object->getData('products'))){
            $table = $this->getTable('lof_designer_product');
            $where = ['designer_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);

            if($quetionProducts = $object->getData('products')){
                $where = ['designer_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                $delete_products = [];
                foreach ($quetionProducts as $k => $_post) {
                    if($this->isExistProduct($k)){
                        $delete_products[] = (int)$k;
                        $data[] = [
                        'designer_id' => (int)$object->getId(),
                        'product_id' => $k,
                        'position' => $_post['product_position']
                        ];
                    }
                }
                if($delete_products){
                    $where_delete = ['product_id IN (?)' => $delete_products];
                    $this->getConnection()->delete($table, $where_delete);
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return parent::_afterSave($object);
    }

    public function isExistProduct($product_id = 0){
        $result = false;
        if($product_id){
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('catalog_product_entity'),
                'COUNT(entity_id)'
                )
            ->where(
                'entity_id = ?',
                (int)$product_id
                );

            $total = (int)$connection->fetchOne($select);
            if($total){
                $result = true;
            }
        }
        return $result;
    }

    public function saveProduct(\Magento\Framework\Model\AbstractModel $object, $product_id = 0) {
        if($object->getId() && $product_id) {
            $table = $this->getTable('lof_designer_product');

            if(is_numeric($product_id)){
                $select = $this->getConnection()->select()->from(
                ['cp' => $table]
                )->where(
                'cp.designer_id = ?',
                (int)$object->getId()
                )->where(
                'cp.product_id = (?)',
                (int)$product_id
                )->limit(1);

                $row_product = $this->getConnection()->fetchAll($select);

                if(!$row_product) { // check if not exists product, then insert it into database
                    $data = [];
                    $data[] = [
                        'designer_id' => (int)$object->getId(),
                        'product_id' => (int)$product_id,
                        'position' => 0
                        ];

                    $this->getConnection()->insertMultiple($table, $data);
                }
            }elseif(is_array($product_id)) {
                $table = $this->getTable('lof_designer_product');
                $where = ['designer_id = ?' => (int)$object->getId(), 'product_id IN (?)' => $product_id];
                $this->getConnection()->delete($table, $where);
  
                $data = [];
                foreach($product_id as $tmp_product_id){
                    $data[] = [
                        'designer_id' => (int)$object->getId(),
                        'product_id' => (int)$tmp_product_id,
                        'position' => 0
                        ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
            return true;
        }
        return false;
    }

    public function deleteDesignersByProduct($product_id = 0) {
        if($product_id) {
            $condition = ['product_id = ?' => (int)$product_id];
            $this->getConnection()->delete($this->getTable('lof_designer_product'), $condition);
            return true;
        }
        return false;
    }

    public function getDesignerIdByName($designer_name = '') {
        if($designer_name) {
            $designer_id = null;
            $table = $this->getTable('lof_designer');

            $select = $this->getConnection()->select()->from(
            ['cp' => $table]
            )->where(
            'cp.name = ?',
            $designer_name
            )->limit(1);

            $row_designer = $this->getConnection()->fetchAll($select);
            if($row_designer) { // check if have designer record

                $designer_id = isset($row_designer[0]['designer_id'])?(int)$row_designer[0]['designer_id']:null;
            }
            return $designer_id;
        }
        return null;
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        if ($id = $object->getId()) {
                $connection = $this->getConnection();
                $select = $connection->select()
                ->from($this->getTable('lof_designer_product'))
                ->where(
                    'designer_id = '.(int)$id
                    );
                $products = $connection->fetchAll($select);
                $object->setData('products', $products);
            } 

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $designerId
     * @return array
     */
    public function lookupStoreIds($designerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_designer_store'),
            'store_id'
            )
        ->where(
            'designer_id = ?',
            (int)$designerId
            );
        return $connection->fetchCol($select);
    }

    public function checkUrlExits(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getStores();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('lof_designer'),
            'designer_id'
            )
        ->where(
            'url_key = ?',
            $object->getUrlKey()
            )
        ->where(
            'designer_id != ?',
            $object->getId()
            );

        $designerIds = $connection->fetchCol($select);
        if(count($designerIds)>0 && is_array($stores)){
            if(in_array('0', $stores)){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
            $stores[] = '0';
            $select = $connection->select()->from(
                $this->getTable('lof_designer_store'),
                'designer_id'
                )
            ->where(
                'designer_id IN (?)',
                $designerIds
                )
            ->where(
                'store_id IN (?)',
                $stores
                );
            $result = $connection->fetchCol($select);
            if(count($result)>0){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
        }
        return $this;
    }
    public function getTotalProducts($designer_id = 0){
        $total = 0;
        if($designer_id){
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTable('lof_designer_product'),
                'COUNT(*)'
                )
            ->where(
                'designer_id = ?',
                (int)$designer_id
                );

            $total = (int)$connection->fetchOne($select);
        }
        return $total;
    }
}