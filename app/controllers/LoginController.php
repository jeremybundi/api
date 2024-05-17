<?php

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function initialize()
    {
        $this->handleCors(); 
        $this->view->disable(); 
    }

    public function indexAction()
    {
        $json = $this->request->getRawBody();

        $data = json_decode($json, true);

        if (isset($data['username'])) {
            $username = $data['username'];

            $user = Users::findFirst([
                'conditions' => 'username = :username:',
                'bind' => ['username' => $username],
            ]);

            if ($user) {
            
                if (password_verify($data['password'], $user->password)) {
         
                    $this->session->set('user_id', $user->id);

                    $userRoles = UserRoles::findFirst([
                        'conditions' => 'user_id = :user_id:',
                        'bind' => ['user_id' => $user->id],
                    ]);

                    if ($userRoles) {
                        $userDetails = [
                            'success' => true,
                            'message' => 'Login successful',
                            'user_name' => $user->name,
                            'role_id' => $userRoles->role_id,
                        ];
                    } else {
                        $userDetails = [
                            'success' => true,
                            'message' => 'Login successful',
                            'user_name' => $user->name,
                            'role_id' => null, 
                        ];
                    }

                    $this->response->setStatusCode(200, 'Login successful');
                    $this->response->setJsonContent($userDetails);
                } else {
                    $this->response->setStatusCode(401, 'Invalid credentials');
                    $data = ['success' => false, 'message' => 'Invalid credentials'];
                    $this->response->setJsonContent($data);
                }
            } else {
                $this->response->setStatusCode(401, 'User not found');
                $data = ['success' => false, 'message' => 'User not found'];
                $this->response->setJsonContent($data);
            }
        } else {
            
            $this->response->setStatusCode(400, 'Invalid request format');
            $data = ['success' => false, 'message' => 'Missing username in request body'];
            $this->response->setJsonContent($data);
            return $this->response;
        }

        return $this->response;
    }

    private function handleCors()
    {
      
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
    }
}
