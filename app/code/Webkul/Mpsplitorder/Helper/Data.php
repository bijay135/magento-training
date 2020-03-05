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
namespace Webkul\Mpsplitorder\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Webkul\Marketplace\Model\ResourceModel\Saleperpartner;

/**
 * Mpsplitorder data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Saleperpartner\CollectionFactory
     */
    private $saleperpartnerCollectionFactory;

    /**
     * @param Magento\Framework\App\Helper\Context        $context
     * @param Filesystem                                  $filesystem
     * @param Magento\Directory\Model\Currency            $currency
     * @param Magento\Customer\Model\Session              $customerSession
     * @param Magento\Store\Model\StoreManagerInterface   $_storeManager
     * @param Saleperpartner\CollectionFactory      $saleperpartnerCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Saleperpartner\CollectionFactory $saleperpartnerCollectionFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->saleperpartnerCollectionFactory = $saleperpartnerCollectionFactory;

        parent::__construct($context);
    }

    public function getIsActive()
    {
        return $this->scopeConfig->getValue('marketplace/mpsplitorder/mpsplitorder_enable');
    }

    public function getIpnNotificationUrl()
    {
        return $this->_urlBuilder->getUrl(
            'mpsplitorder/index/paymentnotify',
            []
        );
    }

    public function getCancelUrl($orderIds = [])
    {
        return $this->_urlBuilder->getUrl(
            'checkout/cart/index',
            ['orderid' => $orderIds]
        );
    }

    public function getSellerDetailById($sellerId = '')
    {
        if ($sellerId!=0) {
            $sellerdetails = $this->saleperpartnerCollectionFactory
            ->create()
            ->addFieldToFilter('seller_id', $sellerId);
            if (!empty($sellerdetails)) {
                foreach ($sellerdetails as $temp) {
                    return [
                        'id' => $temp->getSellerId(),
                        'commission' => $temp->getCommissionRate(),
                    ];
                }
            } else {
                return [
                    'id' => $sellerId,
                    'commission' => $this->scopeConfig->getValue(
                        'marketplace/general_settings/percent',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ),
                ];
            }
        } else {
            return ['id' => 0,'commission' => 0];
        }
    }
}
