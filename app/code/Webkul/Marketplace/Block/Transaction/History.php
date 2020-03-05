<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Block\Transaction;

use Webkul\Marketplace\Model\ResourceModel\Sellertransaction\CollectionFactory;
use Webkul\Marketplace\Model\ResourceModel\Saleperpartner\CollectionFactory as SalePerPartnerCollectionFactory;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var CollectionFactory
     */
    protected $_transactionCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var SalePerPartnerCollectionFactory
     */
    protected $salePerPartnerCollectionFactory;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Sellertransaction\Collection
     */
    protected $_sellerTransactionLists;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CollectionFactory $transactionCollectionFactory
     * @param \Magento\Sales\Model\Order $order
     * @param SalePerPartnerCollectionFactory $salePerPartnerCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $transactionCollectionFactory,
        \Magento\Sales\Model\Order $order,
        SalePerPartnerCollectionFactory $salePerPartnerCollectionFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->_order = $order;
        $this->salePerPartnerCollectionFactory = $salePerPartnerCollectionFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Transactions'));
    }

    public function getCustomerId()
    {
        return $this->_customerSession->getCustomerId();
    }

    /**
     * @return bool|\Webkul\Marketplace\Model\ResourceModel\Sellertransaction\Collection
     */
    public function getAllTransaction()
    {
        if (!($customerId = $this->getCustomerId())) {
            return false;
        }

        if (!$this->_sellerTransactionLists) {
            $ids = [];
            $orderids = [];
            $paramData = $this->getRequest()->getParams();
            $trId = '';
            $filterDataTo = '';
            $filterDataFrom = '';
            $from = null;
            $to = null;

            if (isset($paramData['tr_id'])) {
                $trId = $paramData['tr_id'] != '' ?
                $paramData['tr_id'] : '';
            }
            if (isset($paramData['from_date'])) {
                $filterDataFrom = $paramData['from_date'] != '' ?
                $paramData['from_date'] : '';
            }
            if (isset($paramData['to_date'])) {
                $filterDataTo = $paramData['to_date'] != '' ?
                $paramData['to_date'] : '';
            }

            $collection = $this->_transactionCollectionFactory->create()
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'seller_id',
                ['eq' => $customerId]
            );

            if ($filterDataTo) {
                $todate = date_create($filterDataTo);
                $to = date_format($todate, 'Y-m-d 23:59:59');
            }
            if ($filterDataFrom) {
                $fromdate = date_create($filterDataFrom);
                $from = date_format($fromdate, 'Y-m-d H:i:s');
            }

            if ($trId) {
                $collection->addFieldToFilter(
                    'transaction_id',
                    ['eq' => $trId]
                );
            }

            $collection->addFieldToFilter(
                'created_at',
                ['datetime' => true, 'from' => $from, 'to' => $to]
            );

            $collection->setOrder(
                'created_at',
                'desc'
            );
            $this->_sellerTransactionLists = $collection;
        }

        return $this->_sellerTransactionLists;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllTransaction()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'marketplace.transaction.pager'
            )->setCollection(
                $this->getAllTransaction()
            );
            $this->setChild('pager', $pager);
            $this->getAllTransaction()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl(); // Give the current url of recently viewed page
    }

    /**
     * @return string
     */
    public function getFormatedPrice($price = 0)
    {
        return $this->_order->formatPrice($price);
    }

    /**
     * @return int|float
     */
    public function getRemainTotal()
    {
        $customerId = $this->getCustomerId();
        $collection = $this->salePerPartnerCollectionFactory->create();
        $collection->addFieldToFilter('seller_id', $customerId);
        $total = 0;
        foreach ($collection as $item) {
            $total = $item->getAmountRemain();
        }

        if ($total < 0) {
            $total = 0;
        }

        return $total;
    }
}
