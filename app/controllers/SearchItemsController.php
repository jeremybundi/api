<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class SearchItemsController extends Controller
{
    private function handleCors()
    {
        // Create a new response object
        $response = new Response();

        // Set CORS headers
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    public function indexAction()
    {
        $itemName = $this->request->getQuery('item_name');

        // Validate item_name (optional)
        if (empty($itemName)) {
            $response = $this->handleCors();
            $response->setStatusCode(400, 'Bad Request');
            $response->setJsonContent(["status" => false, "message" => "Missing item_name"]);
            return $response;
        }
    

        // Search for items by item_name
        $itemsModel = new Items();
        $searchResults = $itemsModel->find([
            "conditions" => "item_name LIKE :item_name:",
            "bind" => ["item_name" => "%$itemName%"],
        ]);

        if (empty($searchResults)) {
            $response = $this->handleCors();
            $response->setStatusCode(404, 'Not Found');
            $response->setJsonContent(["status" => false, "message" => "No items found"]);
            return $response;
        }

        $itemData = [];
        foreach ($searchResults as $item) {
            $itemData[] = [
                "id" => $item->id,
                "item_name" => $item->item_name,
                "item_url" => $item->item_url,
                "details" => $item->details,
                "price" => $item->price,
            ];
        }

        $response = $this->handleCors();
        $response->setStatusCode(200, 'OK');
        $response->setContentType('application/json');
        $response->setJsonContent(["status" => true, "data" => $itemData]);

        return $response;
    }
}
