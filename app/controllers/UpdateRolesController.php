<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UpdateRolesController extends Controller
{
    private function handleCors()
    {
        $response = new Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $response;
    }
          //get all
          public function getUsersAllRolesAction()
          {
              $response = $this->handleCors();
              
        
              $users = Users::query()
                  ->columns(['Users.id', 'Users.name','Users.username','Users.email', 'UserRoles.role_id'])
                  ->join('UserRoles', 'UserRoles.user_id = Users.id')
                  ->execute();
      
            
              $usersArray = $users->toArray();
      
              $response->setJsonContent($usersArray);
              return $response;
          }
       //get users by name
       public function getUsersWithRolesAction($name = null)
       {
           $response = $this->handleCors();
           
          
           $query = Users::query()
           ->columns(['Users.id', 'Users.name','Users.username','Users.email', 'UserRoles.role_id'])
           ->join('UserRoles', 'UserRoles.user_id = Users.id');
       
           if ($name) {
               $query->where('Users.name = :name:', ['name' => $name]);
           }
       
           $users = $query->execute();
           $usersArray = $users->toArray();
       
           $response->setJsonContent($usersArray);
           return $response;
       }
       
       
            // Method to update 
        public function updateUserRoleAction($user_id, $new_role_id)
        {
            $response = new Response();

            try {
              
                $userRole = UserRoles::findFirst([
                    'conditions' => 'user_id = :user_id:',
                    'bind'       => ['user_id' => $user_id]
                ]);

                if ($userRole) {
                  
                    $userRole->role_id = $new_role_id;
                    if ($userRole->update()) {
                   
                        $response->setStatusCode(200, "OK");
                        $response->setJsonContent(["status" => "success", "message" => "User role updated successfully."]);
                    } else {
                  
                        $response->setStatusCode(409, "Conflict");
                        $response->setJsonContent(["status" => "error", "message" => "Failed to update user role."]);
                    }
                } else {
                
                    $response->setStatusCode(404, "Not Found");
                    $response->setJsonContent(["status" => "error", "message" => "User role entry not found."]);
                }
            } catch (\Exception $e) {

                $response->setStatusCode(500, "Internal Server Error");
                $response->setJsonContent(["status" => "error", "message" => $e->getMessage()]);
            }

            return $response;
        }
 }