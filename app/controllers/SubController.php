<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class SubController extends Controller
{
    public function postAction($subcategoryId = null)
    {
        $this->view->disable();

        $this->response->setContentType('application/json', 'UTF-8');

        if ($subcategoryId) {
            $items = Items::find([
                'conditions' => 'subcategory_id = :subcategory_id:',
                'bind' => ['subcategory_id' => $subcategoryId],
            ]);

            if (count($items) > 0) {
                $this->response->setStatusCode(200);
                $this->response->setContent(json_encode($items->toArray()));
            } else {
                $this->response->setStatusCode(404);
                $this->response->setContent(json_encode([
                    'message' => 'No items found for subcategory ID: ' . $subcategoryId,
                ]));
            }
        } else {
            $this->response->setStatusCode(400); 
            $this->response->setContent(json_encode([
                'message' => 'Missing required parameter: subcategory_id',
            ]));
        }
    }
} 