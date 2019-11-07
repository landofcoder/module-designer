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
namespace Lof\Designer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	/**
     * @var \Magento\Eav\Model\Entity\Type
     */
	protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    
    /**
     * @var \Magento\Eav\Setup\EavSetupe
     */
    protected $_eavSetup;

    /**
     * @param \Magento\Eav\Setup\EavSetup         $eavSetup         
     * @param \Magento\Eav\Model\Entity\Type      $entityType       
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute 
     */
    public function __construct(
    	\Magento\Eav\Setup\EavSetup $eavSetup,
    	\Magento\Eav\Model\Entity\Type $entityType,
    	\Magento\Eav\Model\Entity\Attribute $catalogAttribute
    	) {
    	$this->_eavSetup = $eavSetup;
    	$this->_entityTypeModel = $entityType;
    	$this->_catalogAttribute = $catalogAttribute;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$entityTypeModel = $this->_entityTypeModel;
    	$catalogAttributeModel = $this->_catalogAttribute;
    	$installer =  $this->_eavSetup;

    	$setup->startSetup();

		/**
		 * Drop table if exists
		 */
		$setup->getConnection()->dropTable($setup->getTable('lof_designer_group'));
		$setup->getConnection()->dropTable($setup->getTable('lof_designer'));
		$setup->getConnection()->dropTable($setup->getTable('lof_designer_store'));

 		/**
 		 * Create table 'lof_designer_group'
 		 */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('lof_designer_group'))
 		->addColumn(
 			'group_id',
 			Table::TYPE_INTEGER,
 			11,
 			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Group ID'
 			)
 		->addColumn(
 			'name',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Group Name'
 			)
 		->addColumn(
 			'url_key',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Group Url Key'
 			)
        ->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
        ->addColumn(
            'shown_in_sidebar',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Show In Sidebar'
            )
        ->setComment('Designer Group');
        $setup->getConnection()->createTable($table);

 		/**
 		 * Create table 'lof_designer'
 		 */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('lof_designer'))
 		->addColumn(
 			'designer_id',
 			Table::TYPE_INTEGER,
 			null,
 			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Designer ID'
 			)
 		->addColumn(
 			'name',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Designer Name'
 			)
 		->addColumn(
 			'url_key',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Designer Url Key'
 			)
 		->addColumn(
 			'description',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Designer Description'
 			)
 		->addColumn(
 			'group_id',
 			Table::TYPE_INTEGER,
 			11,
 			['unsigned' => true, 'nullable' => false],
 			'Group ID'
 			)
 		->addColumn(
 			'image',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Designer Image'
 			)
 		->addColumn(
 			'thumbnail',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Designer Thumbnail'
 			)
 		->addColumn(
 			'page_title',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Designer Page Title'
 			)
 		->addColumn(
 			'meta_keywords',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Meta Keywords'
 			)
 		->addColumn(
 			'meta_description',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Meta Description'
 			)->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Designer Creation Time'
            )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Designer Modification Time'
            )->addColumn(
            'page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Layout'
            )->addColumn(
            'layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Page Layout Update Content'
            )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
                )
            ->addIndex(
                $setup->getIdxName('lof_designer', ['group_id']),
                ['group_id']
                )
            ->addForeignKey(
                $setup->getFkName('lof_designer', 'group_id', 'lof_designer', 'group_id'),
                'group_id',
                $setup->getTable('lof_designer_group'),
                'group_id',
                Table::ACTION_CASCADE
                )
            ->setComment('Designer Information');
            $setup->getConnection()->createTable($table);

 		/**
         * Create table 'lof_designer_store'
         */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('lof_designer_store'))
 		->addColumn(
 			'designer_id',
 			Table::TYPE_INTEGER,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Designer Id'
 			)
 		->addColumn(
 			'store_id',
 			Table::TYPE_SMALLINT,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Store Id'
 			)
 		->addIndex(
 			$setup->getIdxName('lof_designer_store', ['store_id']),
 			['store_id']
 			)
 		->addForeignKey(
 			$setup->getFkName('lof_designer_store', 'designer_id', 'lof_designer', 'designer_id'),
 			'designer_id',
 			$setup->getTable('lof_designer'),
 			'designer_id',
 			Table::ACTION_CASCADE
 			)
 		->addForeignKey(
 			$setup->getFkName('lof_designer_store', 'store_id', 'store', 'store_id'),
 			'store_id',
 			$setup->getTable('store'),
 			'store_id',
 			Table::ACTION_CASCADE
 			)
 		->setComment('Designer Store');
 		$setup->getConnection()->createTable($table);

 		$setup->endSetup();
 	}
 }
