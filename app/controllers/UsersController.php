<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UsersController extends Controller
{
    private function setResponseHeaders(Response $response)
    {
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setContentType('application/json', 'utf-8');
    }

    public function postAction()
    {
        $requestData = $this->request->getJsonRawBody();
        $response = new Response();

        // Set headers
        $this->setResponseHeaders($response);
        if (!isset($requestData->name) || !isset($requestData->email)) {
            $response->setStatusCode(422, 'Unprocessable Entity');
            $response->setJsonContent(["error" => "Name and email are required"]);
            return $response;
        }

        $user = new Users();
        $user->name = $requestData->name;
        $user->phone = $requestData->phone;
        $user->email = $requestData->email;
        $user->username = $requestData->username;
        $user->password = $this->security->hash($requestData->password);

        if ($user->save()) {
            $userRole = new UserRoles();
            $userRole->user_id = $user->id;
            $userRole->role_id = 1; 
            $userRole->save();         


            $response->setStatusCode(201, 'Created');
            $response->setJsonContent(["message" => "User added successfully"]);
        } else {
            $response->setStatusCode(500, 'Internal Server Error');
            $messages = [];
            foreach ($user->getMessages() as $message) {
                $messages[] = $message->getMessage();
            }
            $response->setJsonContent(["error" => "Failed to add user", "details" => $messages]);
        }

        $response->setStatusCode(200, 'success');

        return $response->send();
    }
}









