<?php

use Phalcon\Mvc\Model;

class OrderItems extends Model
{
    public $id;
    public $orderId;
    public $itemId;
    public $quantity;

    public function initialize()
    {
        $this->setSource('order_items'); 
        $this->belongsTo('orderId', 'Orders', 'id', [
            'alias' => 'order'
        ]);
    }
}
