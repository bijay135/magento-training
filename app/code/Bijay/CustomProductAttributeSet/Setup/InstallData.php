<?php

namespace Bijay\CustomProductAttributeSet\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface {
    private $attributeSetFactory;
    private $categorySetupFactory;

    public function __construct( AttributeSetFactory $attributeSetFactory, CategorySetupFactory $categorySetupFactory ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId); // Default attribute set Id

        $data = [
            'attribute_set_name' => 'Custom Attribute Set Code', //attribute set name
            'entity_type_id' => $entityTypeId,
            'sort_order' => 50,
        ];

        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($attributeSetId)->save(); // based on default attribute set
    }
}