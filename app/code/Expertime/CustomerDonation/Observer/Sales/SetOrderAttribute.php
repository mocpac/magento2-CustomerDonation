<?php

/**
 * @author Jo Ng <ngkwokwah@gmail.com>
 */

namespace Expertime\CustomerDonation\Observer\Sales;

/**
 * Update attribute `donation_amount` after order is saved
 */
class SetOrderAttribute implements \Magento\Framework\Event\ObserverInterface
{
    protected $donation;

    public function __construct(
        \Expertime\CustomerDonation\Block\Cart\Donation $donation
    ) {
        $this->donation = $donation;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getData('order');
        $donationAmount = 0;
        $donationSku = $this->donation->getProductSku();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getProduct()->getSku() == $donationSku) {
                $donationAmount = $item->getRowTotal();
                break;
            }
        }
        $order->setDonationAmount($donationAmount);
        $order->save();
    }
}
