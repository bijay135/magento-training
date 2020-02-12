# magento-training


# Magento Architecture & Customization Techniques 

## Create Custom theme based on compser
* Theme must be based on luma theme
* add custome less files to modify them layour and desing (Make it simple just make sure less css witten in less files worn in magento production mode).
* configure grunt to compile less during development.

## create custome module
* Add owl.carousel slider in home page.
    * custom js 
    * make sure slider js is only loaded in home page

* Create custom widget to display 10 product of selected category and make sure to use owl.carousel slider. 
    * Widged title => input
    * Display no of product => input
    * category drop down => Select (list of category in tree structure)
    
* Add system config
    * Add confign to enable and disbale module
    * Add default configuration for Display no of product.
 

## Simple Customization in PLP pages
    * add block in product thumbal which show X% of discount a
    * Add custom cms block in left side bar for ads.
    
## Create Custom modul
* create custom category attribute

# create custom product attribute
    * display in PLP sorting
    * Display in layer navigation
    * Explore how attribute are saved in Flat table, attribute search (frontend and backend)
    * Create Attribute set programitacally
    * Attribute set programitacally
# category attribute
    * Apply desing to product
    * Move CMS block inside right column.
    * schedule desing according to Date
# Catalog product
    * Tax rules and tax zones
    * Add video in product
    * create product.(category associate, image, inventory,custom option)
    * Related product, up-selll and cross-sels (from code).
# PDP page customization
    * Add custom tabs.
    * Remove review form tabs and display below tabs.
    * show proce above "Add to cat".
    * add qty incriment feature (use knockout JS).
# PLP 
    * Add Quick view in PLP
    * Show Dialog message after adding product to cart.
# Cart and order
    * Quote, quote_item, quote_payment, quote_address, quote_shipping
    * Order, order_item, order_payment, order_address, order_shipping