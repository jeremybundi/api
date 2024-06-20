
<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Url as UrlValidator;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\Numericality;

class ItemsController extends Controller
{
    private function handleCors(Response $response)
    {
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
                "category_id"=> $item->category_id,
                "subcategory_id"=> $item->subcategory_id,
                "price" => $item->price,
                "id" => $item->id,
            ];
        }

        $response = new Response();
        $response = $this->handleCors($response);
        $response->setStatusCode(200, 'OK');
        $response->setContentType('application/json');
        $response->setJsonContent(["status" => true, "data" => $itemData]);

        return $response;
    }

    //search by subcategory_id
    public function searchAction($subcategoryId = null)
    {
        $response = new Response();
        $response = $this->handleCors($response);
        $this->view->disable();

        if ($subcategoryId) {
            $items = Items::find([
                'conditions' => 'subcategory_id = :subcategory_id:',
                'bind' => ['subcategory_id' => $subcategoryId],
            ]);

            if (count($items) > 0) {
                $response->setStatusCode(200);
                $response->setJsonContent($items->toArray());
            } else {
                $response->setStatusCode(404); 
                $response->setJsonContent([
                    'message' => 'No items found for subcategory ID: ' . $subcategoryId,
                ]);
            }
        } else {
            $response->setStatusCode(400); 
            $response->setJsonContent([
                'message' => 'Missing required parameter: subcategory_id',
            ]);
        }

        return $response;
    }
      
    //search by id
    public function postAction($id)
    {
        {
            $itemId = $this->dispatcher->getParam('id');
            
            $item = Items::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $itemId],
            ]);
    
            if ($item) {
                $this->response->setJsonContent(['item' => $item]);
            } else {
                $this->response->setStatusCode(404, 'Item not found');
            }
    
            $this->view->disable();
    
            $this->response->send();
        }
    }
     //create items
     public function createAction()
     {
         $response = new Response();
         $response = $this->handleCors($response);
         $requestData = $this->request->getJsonRawBody();
 
         // Validation
         $validator = new Validation();
         
         $validator->add(
             'item_name',
             new PresenceOf(['message' => 'The item name is required.'])
         );
         $validator->add(
             'item_name',
             new RegexValidator([
                 'pattern' => '/^[a-zA-Z\s\'-.]+$/',
                'message' => 'The item name must contain only letters, hyphens, or apostrophes.'
             ])
         );
         
         $validator->add(
             'item_url',
             new PresenceOf(['message' => 'The item URL is required.'])
         );
         $validator->add(
             'item_url',
             new UrlValidator(['message' => 'The item URL must be a valid URL.'])
         );
       
         $validator->add(
             'details',
             new PresenceOf(['message' => 'The details are required.'])
         );
         $validator->add(
             'details',
             new RegexValidator([
                'pattern' => '/^[a-zA-Z\s\'-.]+$/',
                 'message' => 'The item name must contain only letters, hyphens, fullstop or apostrophes.'
             ])
         );
 
         $validator->add(
             'price',
             new PresenceOf(['message' => 'The price is required.'])
         );
         $validator->add(
             'price',
             new Numericality(['message' => 'The price must be a number.'])
         );
 
         $messages = $validator->validate($requestData);
         
         if (count($messages)) {
             foreach ($messages as $message) {
                 $validationErrors[$message->getField()] = $message->getMessage();
             }
             
             $response->setStatusCode(400);
             $response->setJsonContent(["errors" => $validationErrors]);
             return $response;
         }

        $newItem = new Items();
        $newItem->item_name = $requestData->item_name;
        $newItem->item_url = $requestData->item_url;
        $newItem->details = $requestData->details;
        $newItem->price = $requestData->price;
        $newItem->subcategory_id = $requestData->subcategory_id;
        $newItem->category_id = $requestData->category_id;

       
        if ($newItem->save()) {
         
            $response->setStatusCode(201);
            $response->setJsonContent(["message" => "Item added successfully"]);
        } else {
          
            $response->setStatusCode(500);
            $response->setJsonContent(["error" => "Failed to save item"]);
        }

        return $response;
    }
    
}