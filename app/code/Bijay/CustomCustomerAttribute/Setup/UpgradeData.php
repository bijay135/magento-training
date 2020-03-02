<?php

namespace Bijay\CustomCustomerAttribute\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface {
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $eavSetup->updateAttribute(\Magento\Customer\Model\Customer::ENTITY,
                'custom_customer_attribute',
                'label','Custom Customer Attribute Modified'
            );

            $customCustomerAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'custom_customer_attribute');
            $customCustomerAttribute->save();
        }
    }
}