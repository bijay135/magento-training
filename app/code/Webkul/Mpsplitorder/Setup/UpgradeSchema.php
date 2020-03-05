<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitorder
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsplitorder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        $installer->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'base_custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        
        $installer->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        $installer->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'base_custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        $installer->getConnection()->addColumn(
            $setup->getTable('quote'),
            'custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        $installer->getConnection()->addColumn(
            $setup->getTable('quote'),
            'base_custom_discount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => false,
                'LENGTH' =>'12,4',
                'visible'   => false,
                'required'  => true,
                'comment' => 'custom discount'
            ]
        );
        $setup->endSetup();
    }
}
