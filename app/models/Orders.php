<?php

use Phalcon\Mvc\Model;

class Orders extends Model
{
    public $id;
    public $customerName;
    public $phoneNumber;
    public $deliveryAddress;
    public $paymentMethod;
    public $totalPrice;
    public $createdAt;

    public function initialize()
    {
        $this->setSource('orders');
        $this->hasMany('id', 'OrderItems', 'orderId', [
            'alias' => 'items'
        ]);
    }

    public function beforeCreate()
    {
   
        $this->createdAt = date('Y-m-d H:i:s');
    }
}
