<?php

namespace Bijay\CustomProductAttribute\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'custom_produce_attribute_code',
            [
                'group'                 => 'Content',
                'type'                  => 'varchar',
                'label'                 => 'Custom Product Attribute Code',
                'input'                 => 'text',
                'global'                => 1,
                'sort_order'            => 100,
                'visible'               => true,
                'required'              => false,
                'user_defined'          => false,
                'default'               => null,
                'is_visible_in_grid'    => false,
                'is_filterable_in_grid' => false,
                'searchable'            => false,
                'filterable'            => false
            ]
        );
    }
}