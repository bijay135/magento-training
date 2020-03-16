<?php

namespace Bijay\CreateProductCLI\Helper;

use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\State;
use \Symfony\Component\Console\Input\Input;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Catalog\Model\Product\Option;
use \Magento\Catalog\Api\CategoryLinkManagementInterface;

class Product extends \Magento\Framework\App\Helper\AbstractHelper {
    const ARG_COUNT = 'count';
    protected $storeManager;
    protected $state;
    protected $productFactory;
    protected $data;
    protected $productOption;
    protected $categoryLinkManagement;

    public function __construct(Context $context, StoreManagerInterface $storeManager, State $state,
                                ProductFactory $productFactory, Option $productOption,
                                CategoryLinkManagementInterface $categoryLinkManagement) {
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->productFactory = $productFactory;
        $this->productOption = $productOption;
        $this->categoryLinkManagement = $categoryLinkManagement;
        parent::__construct($context);
    }

    public function setData(Input $input) {
        $this->data = $input;
        return $this;
    }

    public function createProduct($count){
        $product = $this->productFactory->create();

        $product->setTypeId('simple');
        $product->setName('Test Product Code - '. date('dmy-his'));
        $product->setAttributeSetId(4);
        $productSKU = time().$count;
        $product->setSku($productSKU);
        $product->setVisibility(4);
        $product->setPrice(1000);
        $product->setStatus(1);
        $product->setWeight(5);
        $product->setTaxClassId(0);
        $product->setStockData(array(
                'use_config_manage_stock' => 0,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'min_sale_qty' => 1,
                'max_sale_qty' => 5,
                'qty' => 100
            )
        );
        $product->setWebsiteIds(array(1));
        // Set product image
        $imagePath = "test_product_code.jpg";
        $product->addImageToMediaGallery($imagePath, array('image','small_image','thumbnail'),false,false);

        $product->save();

        // set product category
        $this->categoryLinkManagement->assignProductToCategories($product->getSku(),[6]);

        // Adding Custom option to product
        $options = array(
            array(
                "sort_order" => 10,
                "title" => "Custom Option 1",
                "price_type" => "fixed",
                "price" => "1500",
                "type" => "field",
                "is_require" => 0
            ),
            array(
                "sort_order" => 20,
                "title" => "Custom Option 2",
                "price_type" => "fixed",
                "price" => "2000",
                "type" => "field",
                "is_require" => 0
            )
        );
        foreach ($options as $option) {
            $product->setHasOptions(1);
            $product->getResource()->save($product);
            $this->productOption->setProductId($product->getId())
                                ->setStoreId($product->getStoreId())
                                ->addData($option);
            $this->productOption->save();
            $product->addOption($this->productOption);
        }

        return $productSKU;
    }

    public function execute() {
        $this->state->setAreaCode('frontend');

        $count = $this->data->getOption(self::ARG_COUNT)?$this->data->getOption(self::ARG_COUNT):1;
        $count = $count > 50 ? 50:$count;

        for($i =1;$i<=$count;$i++){
            $productSKU = $this->createProduct($i);
            echo $i."- Product is created - SKU : ".$productSKU."\n";
        }
    }
}