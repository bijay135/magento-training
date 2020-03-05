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
namespace Webkul\Mpsplitorder\Block\Checkout;

use Magento\Customer\Model\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\App\ObjectManager;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
   
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Session\SessionManager $coreSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->customerSession = $customerSession;
        $this->_coreSession = $coreSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
    }

    public function getOrderArray()
    {
        $result =[];
        $orderIds = explode(',', $this->_coreSession->getData('orderids'));
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $order = $this->getOrderDataById($orderId);
                if ($order->getId()) {
                    $isVisible = !in_array(
                        $order->getState(),
                        $this->_orderConfig->getVisibleOnFrontStatuses()
                    );
                    $result[] = [
                        'is_order_visible' => $isVisible,
                        'view_order_id' => $this->getUrl('sales/order/view/', ['order_id' => $orderId]),
                        'print_url' => $this->getUrl('sales/order/print', ['order_id'=> $orderId]),
                        'can_print_order' => $isVisible,
                        'can_view_order'  => $this->customerSession->isLoggedIn() && $isVisible,
                        'order_id'  => $order->getIncrementId(),
                    ];
                }
            }
        }
        return $result;
    }

    public function removeData()
    {
        $this->_coreSession->unsCustomDiscount();
        $this->_coreSession->unsOrderids();
        $this->_coreSession->unsShippingInfo();
        $this->_coreSession->unsItem();
        $this->_coreSession->unsLastOrderId();
        return "";
    }

    public function getOrderDataById($orderId = null)
    {
        $order=$this->orderFactory->create()->load($orderId);
        return $order;
    }
}
