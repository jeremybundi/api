<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class OrdersController extends ControllerBase
{    
    //get
    public function indexAction()
    {
        $orderNumber = $this->request->getQuery('orderNumber', 'int');
        $startDate = $this->request->getQuery('startDate');
    
      
        $query = Orders::query()
            ->columns([
                'Orders.id',
                'Orders.customerName',
                'Orders.phoneNumber',
                'Orders.deliveryAddress',
                'Orders.paymentMethod',
                'Orders.totalPrice',
                'Orders.createdAt',
                'Items.item_url',
                'Items.item_name',
                'Items.details',
                'OrderItems.quantity'
            ])
            ->join('OrderItems', 'OrderItems.orderId = Orders.id')
            ->join('Items', 'Items.id = OrderItems.itemId');
    
        
        if ($orderNumber) {
            $query->where('Orders.id = :orderNumber:', ['orderNumber' => $orderNumber]);
        }
    

        if ($startDate) {
            $query->andWhere('Orders.createdAt >= :startDate:', ['startDate' => $startDate]);
        }
    
      
        $orders = $query->execute();
    
        $ordersArray = [];
    
        foreach ($orders as $order) {
         
            if (!isset($ordersArray[$order->id])) {
                $ordersArray[$order->id] = [
                    'id' => $order->id,
                    'customerName' => $order->customerName,
                    'phoneNumber' => $order->phoneNumber,
                    'deliveryAddress' => $order->deliveryAddress,
                    'paymentMethod' => $order->paymentMethod,
                    'totalPrice' => $order->totalPrice,
                    'createdAt' => $order->createdAt,
                    'items' => [] // Initialize items array
                ];
            }
    
            
            $ordersArray[$order->id]['items'][] = [
                'item_url' => $order->item_url,
                'item_name'=> $order->item_name,
                'details' => $order->details,
                'quantity' => $order->quantity
            ];
        }
         
      
        $ordersArray = array_values($ordersArray);
    
        
        $this->response->setJsonContent($ordersArray);
        return $this->response;
    }
    
    //post
    public function createAction()
    {
        $response = new Response();

        if ($this->request->isPost()) {
            $orderData = $this->request->getJsonRawBody();
            $order = new Orders();

            
        $phoneNumber = $orderData->phoneNumber;
        if (!preg_match('/^\d{10}$/', $phoneNumber)) {
            $errors[] = 'Phone number must be 10 digits long.';
           
        }

        $customerName = $orderData->customerName;
        if (!preg_match('/^[A-Za-z]+$/', $customerName)) {
            $errors[] = 'Customer name should only contain letters.';
           
        }

            
            $order->customerName = $orderData->customerName;
            $order->phoneNumber = $orderData->phoneNumber;
            $order->deliveryAddress = $orderData->deliveryAddress;
            $order->paymentMethod = $orderData->paymentMethod;
            $order->totalPrice = $orderData->totalPrice;

           
            $this->db->begin();

            if ($order->save()) {
                foreach ($orderData->items as $itemData) {
                    $orderItem = new OrderItems();
                    $orderItem->orderId = $order->id;
                    $orderItem->itemId = $itemData->itemId;
                    $orderItem->quantity = $itemData->quantity;

                    if (!$orderItem->save()) {
                        $this->db->rollback();
                        $errors = [];
                        foreach ($orderItem->getMessages() as $message) {
                            $errors[] = $message->getMessage();
                        }

                        $response->setJsonContent([
                            'status' => 'error',
                            'messages' => $errors
                        ]);
                        return $response;
                    }
                }

                $this->db->commit();

                $response->setJsonContent([
                    'status' => 'success',
                    'message' => 'Order and order items have been created successfully'
                ]);
            } else {
                $errors = [];
                foreach ($order->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                $response->setJsonContent([
                    'status' => 'error',
                    'messages' => $errors
                ]);
            }
        } else {
            $response->setJsonContent([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        return $response;
    }
}
