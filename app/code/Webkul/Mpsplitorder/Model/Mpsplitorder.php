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
namespace Webkul\Mpsplitorder\Model;

use Webkul\Mpsplitorder\Api\Data\MpsplitorderInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Marketplace Mpsplitorder Model
 *
 * @method \Webkul\Mpsplitorder\Model\ResourceModel\Mpsplitorder _getResource()
 * @method \Webkul\Mpsplitorder\Model\ResourceModel\Mpsplitorder getResource()
 */
class Mpsplitorder extends \Magento\Framework\Model\AbstractModel implements MpsplitorderInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Mpsplitorder cache tag
     */
    const CACHE_TAG = 'marketplace_mpsplitorder';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_mpsplitorder';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_mpsplitorder';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Mpsplitorder\Model\ResourceModel\Mpsplitorder');
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSaleperpartner();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Mpsplitorder
     *
     * @return \Webkul\Mpsplitorder\Model\Mpsplitorder
     */
    public function noRouteSaleperpartner()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Mpsplitorder\Api\Data\MpsplitorderInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
