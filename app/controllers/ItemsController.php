<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ItemsController extends Controller
{
    private function handleCors()
    {
        $response = new Response();
        $response->setHeader('Access-Control-Allow-Origin', '*'); // Allow requests from any origin (for development)
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    public function indexAction()
    {
        // Get items
        $itemsModel = new Items();
        $allItems = $itemsModel->getAllItems();

        $itemData = [];
        foreach ($allItems as $item) {
            $itemData[] = [
                "item_name" => $item->item_name,
                "item_url" => $item->item_url,
                "details" => $item->details,
                "price" => $item->price,
                "id" => $item->id,
            ];
        }

        $response = $this->handleCors();
        $response->setStatusCode(200, 'OK');
        $response->setContentType('application/json');
        $response->setJsonContent(["status" => true, "data" => $itemData]);

        return $response;
    }

    public function createAction()
    {
        $requestData = $this->request->getJsonRawBody();

        // Data validation: Check if required fields are present
        if (!isset($requestData->item_name) || !isset($requestData->price)) {
            $response = $this->handleCors();
            $response->setStatusCode(400); // Bad Request
            $response->setJsonContent(["error" => "Invalid data"]);
            return $response;
        }

        // Create a new item
        $newItem = new Items();
        $newItem->item_name = $requestData->item_name;
        $newItem->item_url = $requestData->item_url;
        $newItem->details = $requestData->details;
        $newItem->price = $requestData->price;

        // Save the item to the database
        if ($newItem->save()) {
            // Item saved successfully
            $response = $this->handleCors();
            $response->setStatusCode(201); // Created
            $response->setJsonContent(["message" => "Item added successfully"]);
            return $response;
        } else {
            // Handle database save errors
            $response = $this->handleCors();
            $response->setStatusCode(500);
            $response->setJsonContent(["error" => "Failed to save item"]);
            return $response;
        }
    }

    public function searchAction()
    {
        $itemName = $this->request->getQuery('item_name'); // Get the item_name from the query parameters
         var_dump($this->request->getQuery('item_name'));

         
        // Search for items by item_name
        $itemsModel = new Items();
        $searchResults = $itemsModel->findByItemName($itemName);

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
