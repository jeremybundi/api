<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class SearchItemsController extends Controller
{
    public function searchAction()
    {
        $itemName = $this->request->getQuery('item_name');

      
        $items = Items::findItemsByName($itemName);

      
        $response = new Response();
        $response->setContentType('application/json');

        if ($items->count() > 0) {
            
            $response->setJsonContent([
                'success' => true,
                'message' => 'Item(s) found:',
                'items' => $items->toArray(),
            ]);
        } else {
            // No items found
            $response->setJsonContent([
                'success' => false,
                'message' => 'No items found for the given name.',
            ]);
        }

        return $response;
    }
}
