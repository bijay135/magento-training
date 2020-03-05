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
namespace Webkul\Mpsplitorder\Model\Order\Creditmemo\Total;

class Customdiscount extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
          $order = $creditmemo->getOrder();
        $orderCustomDiscount = $order->getCustomDiscount();

        if ($orderCustomDiscount) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $orderCustomDiscount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $orderCustomDiscount);
        }
        return $this;
    }
}
