<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Regex as RegexValidator;

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

         // Validation setup
    $validation = new Validation();

    // Name validation
    $validation->add(
        'name',
        new RegexValidator([
            'pattern' => '/^[a-zA-Z ]+$/',
            'message' => 'The name must contain only letters'
        ])
    );

        // Email validation
        $validation->add(
            'email',
            new EmailValidator([
                'message' => 'The email is not valid'
            ])
        );

        // Phone validation
        $validation->add(
            'phone',
            new RegexValidator([
                'pattern' => '/^\+?254\d{9}$/',
                'message' => 'The phone number must be a valid Kenyan mobile number'
            ])
        );

        // Username validation
        $validation->add(
            'username',
            new RegexValidator([
                'pattern' => '/^[a-zA-Z0-9_-]+$/',
                'message' => 'The username must contain only alphanumeric characters, underscores, or hyphens'
            ])
        );

        // Password validation
        $validation->add(
            'password',
            new RegexValidator([
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
                'message' => 'The password must be at least 6 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character'
            ])
        );

        // Validate request data
        $messages = $validation->validate($requestData);

        if (count($messages)) {
            // Handle errors
            $errors = [];
            foreach ($messages as $message) {
                $errors[$message->getField()] = $message->getMessage();
            }

            $response->setStatusCode(422, 'Unprocessable Entity');
            $response->setJsonContent(["errors" => $errors]);
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









