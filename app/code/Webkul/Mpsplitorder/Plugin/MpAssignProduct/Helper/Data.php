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
namespace Webkul\Mpsplitorder\Plugin\MpAssignProduct\Helper;

use Magento\Framework\Session\SessionManager;

class Data
{
    /**
     * Initialize dependencies.
     *
     * @param \Webkul\MpAssignProduct\Helper\Data $helper
     */
    public function __construct(
        SessionManager $coreSession
    ) {
        $this->_coreSession = $coreSession;
    }

    public function aroundCollectTotals(
        \Webkul\MpAssignProduct\Helper\Data $subject,
        \Closure $proceed,
        $quote
    ) {
        foreach ($quote->getAllVisibleItems() as $item) {
            $itemId = $item->getId();
            $assignData = $subject->getAssignDataByItemId($itemId);
            $mpassignItemId = (int)$this->_coreSession->getMpAssignItemId();
            if ($mpassignItemId) {
                $assignData = $subject->getAssignDataByItemId($mpassignItemId);
            }
            if ($assignData['assign_id'] > 0) {
                $assignId = $assignData['assign_id'];
                if ($assignData['child_assign_id'] > 0) {
                    $childAssignId = $assignData['child_assign_id'];
                    $price = $subject->getAssocitePrice($assignId, $childAssignId);
                } else {
                    $price = $subject->getAssignProductPrice($assignId);
                }
                $price = $subject->getFinalPrice($price);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->setRowTotal($item->getQty()*$price);
                $item->getProduct()->setIsSuperMode(true);
            }
        }
    }
}
