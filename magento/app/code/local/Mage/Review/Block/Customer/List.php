<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Reviews list block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Customer_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Product reviews collection
     *
     * @var Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     */
    protected function _construct()
    {
        $this->_collection = Mage::getModel('review/review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDateOrder();
    }
    public function getCompletedOrderDetail()
    {
        echo "getCompletedOrderDetail</br>";

        $orders = Mage::getResourceModel('sales/order_collection') 
            ->addFieldToSelect('*') 
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId()) 
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates())) 
            ->addFieldToFilter('status', 'complete')
            ->setOrder('created_at', 'desc') 
        ; 
        return $orders;
    }

    public function getOrderDetailById($order_id)
    {
        $order_detail_list = array();
        $order = Mage::getModel("sales/order")->load($order_id); //load order by order id 
        $ordered_items = $order->getAllVisibleItems();
        $incrementId = $order->getIncrementId();
        // //已评论的order_items
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $reviewed_items = Mage::getModel('review/review')->getOrderItemReviews($customer_id);
        // exit;
        foreach($ordered_items as $item){     //item detail
            if (in_array($item->getItemId(), $reviewed_items)){
                continue;
            }
            $product_id = $item->getProductId();
            $_product = Mage::getModel('catalog/product')->load($product_id);
            $order_detail = array();
            $order_detail = array(
                                'item_id' => $item->getItemId(),
                                'order_id' => $order->getId(),
                                'name' => $item->getName(),
                                'incrementId' => $incrementId,
                                'qty_ordered' => $item->getQtyOrdered(),
                                'create_at' => $item->getCreatedAt(),
                                'product_id' => $item->getProductId(),
                                'image_url' => $_product->getImageUrl(),
                             );
            $order_detail_list[$item->getItemId()] = $order_detail;
        }
        return $order_detail_list;
    }

    public function getOrderUnReviewedItem($customer_id)
    {
        $order_detail_list = $this->getOrderDetailById($order_id);

    }

    public function getOrderReviewedItems($customer_id)
    {
        //已评论的order_items
        $reviewed_items = Mage::getModel('review/review')->getOrderItemReviews($customer_id);
        return $reviewed_items;
    }
    /**
     * Gets collection items count
     *
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_review_list.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @return string
     */
    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    /**
     * Get product link
     *
     * @return string
     */
    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    /**
     * Format date in short format
     *
     * @param $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }
}
