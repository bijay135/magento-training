<?php

namespace Bijay\ProductCollection\Block;

class Products extends \Magento\Framework\View\Element\Template{
    protected $_productCollectionFactory;
    protected $_categoryFactory;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = [],
                                \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
                                \Magento\Catalog\Model\CategoryFactory $categoryFactory) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    public function getProductCollection() {
        /* Factory Method */
        $productCollection = $this->_productCollectionFactory->create();

        // Filtering using attribute
        $productCollection->addAttributeToSelect('*');

        // fetching fixed amount of products
        $productCollection->setPageSize(10);

        // sort collection
        // $productCollection->setOrder('price', 'ASC');

        // filter using category
        // $category = $this->_categoryFactory->create()->load('6');
        // $productCollection->addCategoryFilter($category);

        // custom filters
        // $productCollection->addAttributeToFilter('status', array('eq' => 1));
        // $productCollection->addAttributeToFilter('status', array('neq' => 1));
        // $productCollection->addAttributeToFilter('sku', array('like' => '%charger%'));
        // $productCollection->addAttributeToFilter('sku', array('nlike' => '%charger%'));
        // $productCollection->addAttributeToFilter('description',  array('null' => true));
        // $productCollection->addAttributeToFilter('description',  array('notnull' => true));

        // filter current website products
        $productCollection->addWebsiteFilter();

        // filter current store products
        $productCollection->addStoreFilter();

        foreach ($productCollection as $product) {
            echo $product->getId().'. '.$product->getName().'<br><br>';
        }
    }
}