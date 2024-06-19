<?php

use Phalcon\Mvc\Controller;

class SearchController extends Controller
{
    public function indexAction()
    {
        $itemName = $this->dispatcher->getParam('item_name');

        if (!ctype_alpha($itemName)) {
            $response = [
                'error' => 'Invalid item name. Only letters are allowed.',
            ];
        } else {
            $items = Items::query()
                ->where('item_name LIKE :item_name:')
                ->bind(['item_name' => "%$itemName%"])
                ->execute();

            $response = [
                'items' => $items->toArray(),
            ];
        }

        $this->response->setContentType('application/json', 'UTF-8');
        return json_encode($response);
    }
}

