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
namespace Webkul\Mpsplitorder\Model\Order\Invoice\Total;

class Customdiscount extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order=$invoice->getOrder();
        $orderCustomDiscount = $order->getCustomDiscount();
        if ($orderCustomDiscount && count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal() - $orderCustomDiscount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $orderCustomDiscount);
        }
        return $this;
    }
}
