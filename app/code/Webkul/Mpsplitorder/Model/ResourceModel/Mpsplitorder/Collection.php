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
namespace Webkul\Mpsplitorder\Model\ResourceModel\Mpsplitorder;

use \Webkul\Mpsplitorder\Model\ResourceModel\AbstractCollection;

/**
 * Webkul Mpsplitorder ResourceModel Mpsplitorder collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'index_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Mpsplitorder\Model\Mpsplitorder', 'Webkul\Mpsplitorder\Model\ResourceModel\Mpsplitorder');
        $this->_map['fields']['entity_id'] = 'main_table.index_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}
