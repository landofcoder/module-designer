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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /**
             * Create table 'lof_designer_product'
             */
            $designerProductTable = $installer->getConnection()->newTable(
                $installer->getTable('lof_designer_product')
            )->addColumn(
                'designer_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Designer ID'
            )->addColumn(
                'product_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Product ID'
            )->addColumn(
                'position',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => true],
                'Position'
            )->setComment(
                'Lof Designer To Product Linkage Table'
            );
            $installer->getConnection()->createTable($designerProductTable);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            $designerProductTable = $setup->getTable('lof_designer_product');
            $designerTable = $setup->getTable('lof_designer');

            $setup->getConnection()->addColumn(
                $designerTable,
                'contact_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact Name',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'company_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Company Name',
                    'length' => 150,
                    'default' => ""
                ]
            );


            $setup->getConnection()->addColumn(
                $designerTable,
                'email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact email address',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'telephone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact telephone',
                    'length' => 60,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'website',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact website',
                    'length' => 100,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'street',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact street',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'city',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact city',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'region_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact region id',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'region',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact region',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'postcode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact postcode',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'birthday_info',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Birthday Info',
                    'length' => 150,
                    'default' => ""
                ]
            );

            $setup->getConnection()->addColumn(
                $designerTable,
                'country_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Designer Contact country id',
                    'length' => 50,
                    'default' => ""
                ]
            );

            
        }
        $installer->endSetup();
    }
}
