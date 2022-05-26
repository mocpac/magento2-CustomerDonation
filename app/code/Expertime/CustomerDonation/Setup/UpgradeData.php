<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    private $salesSetupFactory;

    public function __construct(\Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory)
    {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute(
                'order',
                'donation_amount',
                [
                    'type' => 'decimal',
                    'visible' => false,
                    'required' => false,
                    'grid' => true,
                ]
            );
        }
    }

    //downgrade
}
