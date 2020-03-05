<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpsplitorder
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Mpsplitorder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;

class SalesOrderSaveAfterObserver implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        SessionManager $session
    ) {
        $this->_objectManager = $objectManager;
        $this->session = $session;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customDiscount = $observer->getQuote()->getCustomDiscount();
        $code = $this->session->getDiscountDescription();
        $order = $observer->getOrder();
        $orderDiscount = $order->getDiscountAmount();
        $order->setCustomDiscount($customDiscount);
        $order->setDiscountAmount($customDiscount);
        $order->setBaseDiscountAmount($customDiscount);
        $order->setBaseGrandTotal($order->getBaseGrandTotal()-$customDiscount-$orderDiscount);
        $order->setGrandTotal($order->getGrandTotal()-$customDiscount-$orderDiscount);
        $order->setDiscountDescription($code);
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $orderAddress = $order->getShippingAddress();
            $orderAddress->setCustomDiscount($customDiscount);
        }
        $orderBillingAddress = $order->getBillingAddress();
        $orderBillingAddress->setCustomDiscount($customDiscount);
        $order->save();
    }
}
