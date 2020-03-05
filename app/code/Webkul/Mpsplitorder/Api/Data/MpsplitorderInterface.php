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
namespace Webkul\Mpsplitorder\Api\Data;

/**
 * Mpsplitorder Saleperpartner interface.
 * @api
 */
interface MpsplitorderInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID    = 'index_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Mpsplitorder\Api\Data\SaleperpartnerInterface
     */
    public function setId($id);
}
