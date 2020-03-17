<?php
namespace ego\tests\enums;

use ego\tests\TestCase;
use ego\enums\Order;

class OrderTest extends TestCase
{
    public function testGetName()
    {
        $this->assertEquals('Awaiting Payment', Order::getName(Order::AWAITING_PAYMENT));
        $this->assertEquals('unknown', Order::getName(-1));
    }

    public function testGetUnionStatusName()
    {
        $this->assertEquals('Pending', Order::getUnionStatusName(Order::UNION_PENDING));
        $this->assertEquals('unknown', Order::getUnionStatusName(-1));
    }
    
    public function testGetParentOrderStatus()
    {
        
        $orderInfo = [];
        $this->assertTrue(!Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus1' => Order::AWAITING_PAYMENT,],['orderStatus2' => Order::AWAITING_PAYMENT,]];
        $this->assertTrue(!Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::AWAITING_PAYMENT,],['orderStatus' => Order::AWAITING_PAYMENT,]];
        $this->assertEquals(Order::UNION_AWAITING_PAYMENT, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::PENDING,],['orderStatus' => Order::PENDING,]];
        $this->assertEquals(Order::UNION_PENDING, Order::getParentOrderStatus($orderInfo));

        $orderInfo = [['orderStatus' => Order::PAID,],['orderStatus' => Order::TO_BE_SHIPPED,]];
        $this->assertEquals(Order::UNION_TO_BE_SHIPPED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::TO_BE_SHIPPED,],['orderStatus' => Order::TO_BE_SHIPPED,]];
        $this->assertEquals(Order::UNION_TO_BE_SHIPPED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::TO_BE_SHIPPED,],['orderStatus' => Order::SHIPPED,]];
        $this->assertEquals(Order::UNION_AWAITNG_RECEIVED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::RECEIVED,],['orderStatus' => Order::SHIPPED,]];
        $this->assertEquals(Order::UNION_AWAITNG_RECEIVED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::TO_BE_SHIPPED,],['orderStatus' => Order::RECEIVED,]];
        $this->assertEquals(Order::UNION_AWAITNG_RECEIVED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::RECEIVED,],['orderStatus' => Order::RECEIVED,]];
        $this->assertEquals(Order::UNION_RECEIVED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::CANCELED,],['orderStatus' => Order::CANCELED,]];
        $this->assertEquals(Order::UNION_CANCELED, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::REFUND,],['orderStatus' => Order::REFUND,]];
        $this->assertEquals(Order::UNION_CANCELED_REFUNDING, Order::getParentOrderStatus($orderInfo));
        
        $orderInfo = [['orderStatus' => Order::REFUND_SUCCESS,],['orderStatus' => Order::REFUND_SUCCESS,]];
        $this->assertEquals(Order::UNION_CANCELED_REFUND_SUCCESS, Order::getParentOrderStatus($orderInfo));
        
    }
    
    public function testIsNormalProcessOrder()
    {
        $this->assertTrue(Order::isNormalProcessOrder(Order::TO_BE_SHIPPED));
        $this->assertTrue(!Order::isNormalProcessOrder(Order::CANCELED));
        $this->assertTrue(!Order::isNormalProcessOrder(-1));
    }
    
    public function testGetFloatValue()
    {
        $this->assertEquals('0.00', Order::getFloatValue(0, 2));
        
        $this->assertEquals('2.11', Order::getFloatValue(2.112233, 2));
        $this->assertEquals('2.112', Order::getFloatValue(2.112233, 3));
        
        $this->assertEquals('-2.11', Order::getFloatValue(-2.112233, 2));
        $this->assertEquals('-2.112', Order::getFloatValue(-2.112233, 3));
        
        $this->assertEquals('0', Order::getFloatValue(0, 0));
        $this->assertEquals('0', Order::getFloatValue(0, 'sss'));
        $this->assertEquals('0', Order::getFloatValue('sss  ', 'sss'));
        $this->assertEquals('0.10000000000', Order::getFloatValue(0.1, 11));
    }
}
