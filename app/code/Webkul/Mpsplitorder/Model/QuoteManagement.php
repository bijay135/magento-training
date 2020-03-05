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
//@codingStandardsIgnoreFile
namespace Webkul\Mpsplitorder\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Quote\Model\Quote\Address\ToOrder as ToOrderConverter;
use Magento\Quote\Model\Quote\Address\ToOrderAddress as ToOrderAddressConverter;
use Magento\Quote\Model\Quote\Item\ToOrderItem as ToOrderItemConverter;
use Magento\Quote\Model\Quote\Payment\ToOrderPayment as ToOrderPaymentConverter;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CurrencyInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class QuoteManagement extends \Magento\Quote\Model\QuoteManagement
{
    /**
     * @var Webkul\Mpmangopay\Helper\Data
     */
    protected $_mpSplitOrderHelper;

    /**
     * $checkoutSession.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @param EventManager $eventManager
     * @param SubmitQuoteValidator $quoteValidator
     * @param OrderFactory $orderFactory
     * @param OrderManagement $orderManagement
     * @param CustomerManagement $customerManagement
     * @param ToOrderConverter $quoteAddressToOrder
     * @param ToOrderAddressConverter $quoteAddressToOrderAddress
     * @param ToOrderItemConverter $quoteItemToOrderItem
     * @param ToOrderPaymentConverter $quotePaymentToOrderPayment
     * @param UserContextInterface $userContext
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerModelFactory
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param QuoteFactory $quoteFactory
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Store\Model\StoreManagerInterface              $storeManager
     * @param \Magento\Framework\UrlInterface                         $urlBuilder
     * @param \Magento\Checkout\Model\Session                         $checkoutSession
     * @param \Magento\Framework\ObjectManagerInterface               $objectManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */

    public function __construct(
        EventManager $eventManager,
        // \Magento\Quote\Model\QuoteValidator $quoteValidator, 
        \Magento\Quote\Model\SubmitQuoteValidator $quoteValidator,
        OrderFactory $orderFactory,
        OrderManagement $orderManagement,
        \Magento\Quote\Model\CustomerManagement $customerManagement,
        ToOrderConverter $quoteAddressToOrder,
        ToOrderAddressConverter $quoteAddressToOrderAddress,
        ToOrderItemConverter $quoteItemToOrderItem,
        ToOrderPaymentConverter $quotePaymentToOrderPayment,
        UserContextInterface $userContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\Mpsplitorder\Helper\Data $mpSplitOrderHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Checkout\Model\Session $checkoutSession,
        SessionManager $coreSession,
        DateTime $date,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\Mpsplitorder\Model\MpsplitorderFactory $mpsplitorderFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Webkul\Mpsplitorder\Logger\Mpsplitorder $splitorderLogger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository = null
    ) {
        $this->_objectManager = $objectManager;
        $this->_mpSplitOrderHelper = $mpSplitOrderHelper;
        $this->_messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->_coreSession = $coreSession;
        $this->_date = $date;
        $this->quoteFactory = $quoteFactory;
        $this->mpProductFactory = $mpProductFactory;
        $this->productFactory = $productFactory;
        $this->mpsplitorderFactory = $mpsplitorderFactory;
        $this->addressFactory = $addressFactory;
        $this->splitorderLogger = $splitorderLogger;
        $this->addressRepository = $addressRepository ?: ObjectManager::getInstance()
            ->get(\Magento\Customer\Api\AddressRepositoryInterface::class);
        parent::__construct(
            $eventManager,
            $quoteValidator,
            $orderFactory,
            $orderManagement,
            $customerManagement,
            $quoteAddressToOrder,
            $quoteAddressToOrderAddress,
            $quoteItemToOrderItem,
            $quotePaymentToOrderPayment,
            $userContext,
            $quoteRepository,
            $customerRepository,
            $customerModelFactory,
            $quoteAddressFactory,
            $dataObjectHelper,
            $storeManager,
            $checkoutSession,
            $customerSession,
            $accountManagement,
            $quoteFactory
        );
    }

    public function placeOrder($cartId, PaymentInterface $paymentMethod = null)
    {
        try {
            $shippingAll = $this->_coreSession->getShippingInfo();
            $quote = $this->checkoutSession->getQuote();
            $discount = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
            $couponCode = $quote->getCouponCode();
            $percent = round(($discount*100)/$quote->getSubtotal(), 2);
            //Check module enable
            if ($this->_mpSplitOrderHelper->getIsActive() == 0) {
                $orderId = parent::placeOrder($quote->getId());
                return $orderId;
            }
            $finalArray = [];
            $itemArray = [];
            $splitShip = 0;
            foreach ($quote->getAllItems() as $item) {
                $request = [];
                if ($item->getParentItem()) {
                    if ($item->getProductType()=="simple") {
                        $splitShip++;
                    }
                    continue;
                } else {
                    if ($item->getProductType()=="simple") {
                        $splitShip++;
                    }
                    foreach ($item->getOptions() as $option) {
                        if ($option->getCode()=="info_buyRequest") {
                            $value = json_decode($option->getValue(), true);
                            $value['qty'] = $item->getQty();
                            $request[]  = $value;
                        }
                    }
                }

                $id = 0;
                $rowTotal = $item->getRowTotal();
                $marketplaceCollection = $this->mpProductFactory->create()
                    ->getCollection()
                    ->addFieldToFilter(
                        'mageproduct_id',
                        $item->getProductId()
                    );

                foreach ($marketplaceCollection as $vendor) {
                    $id = $vendor->getSellerId();
                }
                //Check Assign Seller
                if (isset($request[0]['mpassignproduct_id']) && $request[0]['mpassignproduct_id']!="") {
                    $sellerId = $this->_objectManager->create(
                        'Webkul\MpAssignProduct\Helper\Data'
                    )->getAssignSellerIdByAssignId($request[0]['mpassignproduct_id']);
                    $id = $sellerId;
                    if (isset($request[0]['associate_id']) && $request[0]['associate_id']) {
                        $price = $this->_objectManager->create(
                            'Webkul\MpAssignProduct\Helper\Data'
                        )
                        ->getAssocitePrice($request[0]['mpassignproduct_id'], $request[0]['associate_id']);
                    } else {
                        $price = $this->_objectManager->create(
                            'Webkul\MpAssignProduct\Helper\Data'
                        )->getAssignProductPrice($request[0]['mpassignproduct_id']);
                    }
                    $request[0]['price'] = $price;
                    $rowTotal = $price;
                }

                $finalArray[$id][] = [
                    'product' => $item->getProductId(),
                    'request' => $request[0],
                    'qty'=>$item->getQty(),
                    'item_id' => $item->getId(),
                ];


                $itemArray[] = [
                    'id' => $item->getId(),
                    'row_total' => $rowTotal,
                    'product_id' => $item->getProductId(),
                    'tax_amount' => $item->getTaxAmount(),
                    'seller_id' => $id
                ];
            }
            // Check only one seller product
            if (count($finalArray) == 1) {
                $newDis = round(($quote->getSubtotal()*$percent)/100, 2);
                $quote->setCustomDiscount($newDis);
                $this->_coreSession->setData('discount_description', $couponCode);
                // Collect Totals & Save Quote
                $quote->collectTotals()->save();
                $orderId = parent::placeOrder($quote->getId());
                return $orderId;
            }

            $orderedArray = [
                'shipping_method' => $quote->getShippingAddress()->getShippingMethod(),
                'shipping_tax_amount' => $quote->getShippingAddress()->getData('shipping_tax_amount'),
                'shipping_amount' =>$quote->getShippingAddress()->getShippingAmount(),
            ];
            $currenQuoteId=$this->checkoutSession->getQuoteId();
            $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();
            $currency = $quote->getCurrency();
            $billingAddress1 = $quote->getBillingAddress()->getData();
            $shippingAddress1 = $quote->getShippingAddress()->getData();
            $shippingAmountPerItem = $shippingAddress1['shipping_amount'] / $splitShip;
            $checkoutMethod = $quote->getCheckoutMethod();
            $paymentMethod = $quote->getPayment()->getMethod();
            $quote->setIsActive(0)->delete()->save();
            $quote = $this->quoteFactory->create()->load($currenQuoteId);
            $quote->setIsActive(0)->delete();
            $orderIds = [];
            $store = $this->storeManager->getStore();
            $customer = $this->checkCustomer($billingAddress1);
            $this->_coreSession->setIsSpiltOrder(1);
            $mpSelectedMethods = $this->_coreSession->getSelectedMethods();
            foreach ($finalArray as $sId => $items) {
                $shippingAmount = 0;
                $this->_coreSession->unsMpAssignItemId();
                // Start New Sales Order Quote
                $newquote = $this->quoteFactory->create();
                $newquote->setStore($store);

                // Set Sales Order Quote Currency
                $newquote->setCurrency($currency);
                $newquote->setCheckoutMethod($checkoutMethod);

                // Assign Customer To Sales Order Quote
                // Configure Notification
                $newquote->setSendCconfirmation(1);
                foreach ($items as $item) {
                    $shippingAmount += $shippingAmountPerItem;
                    $product = $this->productFactory->create()->load($item['product']);
                    if (isset($item['request']['mpassignproduct_id']) && $item['request']['mpassignproduct_id']!="") {
                        $product->setPrice($item['request']['price']);
                        $this->_coreSession->setMpAssignItemId($item['item_id']);
                    }
                    //check for Advertisment product
                    foreach ($item['request'] as $req) {
                        if (isset($req['block_position']) && $req['block_position']!="") {
                            $product->setPrice($req['price']);
                        }
                    }
                    if (isset($item['request']['options'])) {
                        foreach ($item['request']['options'] as $tempKey => $tempOptions) {
                            if (isset($tempOptions['date_internal'])) {
                                $item['request']['options'][$tempKey] = $tempOptions['date_internal'];
                            }
                        }
                    }

                    $newquote->addProduct($product, new \Magento\Framework\DataObject($item['request']), 'full');
                }

                // Set Sales Order Billing Address
                $billingAddress = $newquote->getBillingAddress()->addData(
                    [
                        'customer_id' => $billingAddress1['customer_id'],
                        'address_type' => $billingAddress1['address_type'],
                        'firstname' => $billingAddress1['firstname'],
                        'lastname' =>$billingAddress1['lastname'],
                        'email' => $billingAddress1['email'],
                        'street' => $billingAddress1['street'],
                        'city' => $billingAddress1['city'],
                        'country_id' => $billingAddress1['country_id'],
                        'region_id' => $billingAddress1['region_id'],
                        'postcode' => $billingAddress1['postcode'],
                        'telephone' => $billingAddress1['telephone'],
                        'save_in_address_book' => $billingAddress1['save_in_address_book'],
                        'same_as_billing' => $billingAddress1['same_as_billing'],
                    ]
                );
                // Set Sales Order Shipping Address
                $shippingAddress = $newquote->getShippingAddress()->addData(
                    [
                        'customer_id' => $shippingAddress1['customer_id'],
                        'address_type' => $shippingAddress1['address_type'],
                        'firstname' => $shippingAddress1['firstname'],
                        'lastname' =>$shippingAddress1['lastname'],
                        'email' => $shippingAddress1['email'],
                        'street' => $shippingAddress1['street'],
                        'city' => $shippingAddress1['city'],
                        'country_id' => $shippingAddress1['country_id'],
                        'region_id' => $shippingAddress1['region_id'],
                        'postcode' => $shippingAddress1['postcode'],
                        'telephone' => $shippingAddress1['telephone'],
                        'same_as_billing' => $shippingAddress1['same_as_billing'],
                    ]
                );

                if ($checkoutMethod == 'guest') {
                    $newquote->setCustomerId(null)
                        ->setCustomerEmail($billingAddress1['email'])
                        ->setCustomerIsGuest(true)
                        ->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
                } else {
                    $newquote->assignCustomerWithAddressChange($customer, $billingAddress, $shippingAddress);
                }
                // Collect Rates and Set Shipping & Payment Method
                if ($shippingAddress1['shipping_method']!='mpmultishipping_mpmultishipping') {
                    if (!empty($shippingAll)) {
                        try {
                            $isMpShipping = $this->isMpShipping($shippingAddress1['shipping_method']);

                            if (is_array($isMpShipping)) {
                                $subMethod = $isMpShipping[0];
                                $method = $isMpShipping[1];
                                foreach ((array)$shippingAll[$method] as $key1 => $value1) {
                                    if ($sId == $value1['seller_id']) {
                                        $shippingAmount = $value1['submethod'][$subMethod]['base_amount'];
                                        break;
                                    }
                                }
                            } elseif (strpos($shippingAddress1['shipping_method'], "wkpickup")!==false) {
                                $selectedWkpickups = $this->_coreSession->getSelectedPickupMethods();
                                if (is_array($selectedWkpickups) && !empty($selectedWkpickups)) {
                                    $shippingAmount = 0;
                                    foreach ($selectedWkpickups as $value1) {
                                        $value1 = (array) $value1;
                                        if ($sId == $value1['sellerid']) {
                                            $shippingAmount += $value1['baseamount'];
                                        }
                                    }
                                }
                            } else {
                                $shippingData = [];
                                $shippingData = [
                                    'title' => $shippingAddress1['shipping_description'],
                                    'cost'  => $shippingAmount
                                ];
                                $this->checkoutSession->setData('shippingInfo', $shippingData);
                                $shippingMethod = 'splitship_splitship';
                                $shippingAddress1['shipping_method']="splitship_splitship";
                            }
                        } catch (\Exception $e) {
                            $this->_messageManager->addError($e->getMessage());
                        }
                    } else {
                        $shippingData = [];
                        $shippingData = [
                            'title' => $shippingAddress1['shipping_description'],
                            'cost'  => $shippingAmount
                        ];
                        $this->checkoutSession->setData('shippingInfo', $shippingData);
                        $shippingMethod = 'splitship_splitship';
                        $shippingAddress1['shipping_method']="splitship_splitship";
                    }
                } else {
                    if (is_array($mpSelectedMethods) && !empty($mpSelectedMethods)) {
                        $shippingAmount = 0;
                        foreach ($mpSelectedMethods as $value1) {
                            $value1 = (array) $value1;
                            if ($sId == $value1['sellerid']) {
                                $shippingAmount += $value1['baseamount'];
                            }
                        }
                    }
                }
                $this->checkoutSession->replaceQuote($newquote);
                $shippingAddress = $newquote->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)
                                ->collectShippingRates()
                                ->setShippingMethod($shippingAddress1['shipping_method']);

                $newquote->setPaymentMethod($paymentMethod);
                $newquote->setInventoryProcessed(false);
                $newquote->save();

                $address = $newquote->getShippingAddress();

                $rates = $address->collectShippingRates()
                ->getGroupedAllShippingRates();
                foreach ($rates as $carrier) {
                    foreach ($carrier as $rate) {
                        $rate->setPrice($shippingAmount);
                        $rate->save();
                    }
                }
                $address->setCollectShippingRates(false);
                $address->save();

                // Set Sales Order Payment
                $newquote->getPayment()->importData(['method' => $paymentMethod]);
                $newquote->setIsActive(1);
                $newDis = round(($newquote->getSubtotal()*$percent)/100, 2);
                $newquote->setCustomDiscount($newDis);
                $this->_coreSession->setData('discount_description', $couponCode);
                // Collect Totals & Save Quote
                $newquote->collectTotals()->save();

                // Create Order From Quote
                $newquote = $this->quoteRepository->get($newquote->getId());
                $orderId = parent::placeOrder($newquote->getId());
                $orderIds[] = $orderId;
                $lastOrderId = $orderId;
                $newquote->removeAllItems()->save();
                $newquote = $service = null;
            }

            $quote = $this->checkoutSession->getQuote();
            $this->checkoutSession->setData('shippingInfo', 0);
            $this->_coreSession->setData('orderids', implode(',', $orderIds));
            $this->_coreSession->setData('item', $itemArray);
            $this->_coreSession->setData('shipping_info', $orderedArray);
            $this->_coreSession->setData('lastOrderId', $lastOrderId);
            $quote->removeAllItems()->save();

            //save data in Mpsplitorder table
            $splitOrderCollection = $this->mpsplitorderFactory->create();
            $splitOrderCollection->setOrderIds(implode(',', $orderIds));
            $splitOrderCollection->setLastOrderId($lastOrderId);
            $this->_coreSession->unsIsSpiltOrder();
            $splitOrderCollection->setPaymentStatus(0);
            $splitOrderCollection->save();
        } catch (\Exception $e) {
            $this->splitorderLogger->info($e->getMessage());
            $this->splitorderLogger->info($e->getTraceAsString());
            $this->_messageManager->addError($e->getMessage());
            $this->_coreSession->unsIsSpiltOrder();
        }
    }

    /**
     * checkCustomer Validate Customer
     * @param  Mixed $billingAddress1 Billing address from quote
     * @return Mage_Customer_Model
     */
    public function checkCustomer($billingAddress1)
    {
        $quote = $this->checkoutSession->getQuote();
        $email = $billingAddress1['email'];
        $firstname = $billingAddress1['firstname'];
        $lastname = $billingAddress1['lastname'];
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $store = $this->storeManager->getStore();
        $customer=$this->customerModelFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);
        if ($customer->getId()=="") {
            if ($quote->getCheckoutMethod() == 'register') {
                     $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setEmail($email)
                    ->setPassword($email);
                    $customer->save();

                $this->customerSession->loginById($customer->getId());
                $customerAddress = $this->addressFactory->create();
                $cusAddress = [
                    'firstname' => $billingAddress1['firstname'],
                    'middlename' => $billingAddress1['middlename'],
                    'lastname' =>$billingAddress1['lastname'],
                    'email' => $billingAddress1['email'],
                    'suffix' => $billingAddress1['suffix'],
                    'street' => $billingAddress1['street'],
                    'city' => $billingAddress1['city'],
                    'country_id' => $billingAddress1['country_id'],
                    'region_id' => $billingAddress1['region_id'],
                    'postcode' => $billingAddress1['postcode'],
                    'telephone' => $billingAddress1['telephone'],
                    'fax' => $billingAddress1['fax'],
                ];
                $customerAddress->setData($cusAddress)
                    ->setCustomerId($customer->getId())
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');
                $customerAddress->save();
                $customer = $this->customerRepository->getById($customer->getId());
            }
        } else {
            $customer = $this->customerRepository->getById($customer->getId());
            $billing = $quote->getBillingAddress();
            $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

            $hasDefaultBilling = (bool)$customer->getDefaultBilling();
            $hasDefaultShipping = (bool)$customer->getDefaultShipping();

            if ($shipping && !$shipping->getSameAsBilling()
                && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
            ) {
                $shippingAddress = $shipping->exportCustomerAddress();
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $shippingAddress->setIsDefaultShipping(true);
                    $hasDefaultShipping = true;
                    if (!$hasDefaultBilling && !$billing->getSaveInAddressBook()) {
                        $shippingAddress->setIsDefaultBilling(true);
                        $hasDefaultBilling = true;
                    }
                }
                //save here new customer address
                $shippingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($shippingAddress);
                $quote->addCustomerAddress($shippingAddress);
                $shipping->setCustomerAddressData($shippingAddress);
                $shipping->setCustomerAddressId($shippingAddress->getId());
            }

            if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
                $billingAddress = $billing->exportCustomerAddress();
                if (!$hasDefaultBilling) {
                    //Make provided address as default shipping address
                    if (!$hasDefaultShipping) {
                        //Make provided address as default shipping address
                        $billingAddress->setIsDefaultShipping(true);
                    }
                    $billingAddress->setIsDefaultBilling(true);
                }
                $billingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($billingAddress);
                $quote->addCustomerAddress($billingAddress);
                $billing->setCustomerAddressData($billingAddress);
                $billing->setCustomerAddressId($billingAddress->getId());
            }
            if ($shipping && !$shipping->getCustomerId() && !$hasDefaultBilling) {
                $shipping->setIsDefaultBilling(true);
            }
        }
        return $customer;
    }

    public function isMpShipping($shippingMethod)
    {
        $subMethod = 0;
        $shippingWithSubmethod = ["mpfedex", "mpups", "marketplaceusps", "mpcanadapost",
                                 "mpfastway", "mpcorreios", "mparamex", "mpdhl", "mpfrenet", "webkulshipping"];
        $shippingWithoutSubmethod = ["mpfixrate", "webkulmpperproduct", "mppercountry", "mpfreeshipping"];

        foreach ($shippingWithoutSubmethod as $method) {
            if (strpos($shippingMethod, $method)!==false) {
                return [$subMethod, $method];
            }
        }
        foreach ($shippingWithSubmethod as $method) {
            if (strpos($shippingMethod, $method)!==false) {
                $subMethod = substr($shippingMethod, strpos($shippingMethod, "_")+1);
                return [$subMethod, $method];
            }
        }
        return false;
    }
}
